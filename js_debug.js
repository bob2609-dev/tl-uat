/**
 * JavaScript Debugging Utility for TestLink
 * Logs debug information to the console and sends it to the server
 */

const Debugger = {
    enabled: true,
    logFile: 'js_debug.log',
    logs: [],
    maxLogSize: 1000, // Max number of log entries to keep in memory
    
    /**
     * Initialize the debugger
     */
    init() {
        if (!this.enabled) return;
        
        // Override console methods
        const originalConsole = {
            log: console.log,
            error: console.error,
            warn: console.warn,
            info: console.info,
            debug: console.debug
        };
        
        // Override console methods to capture logs
        ['log', 'error', 'warn', 'info', 'debug'].forEach(method => {
            console[method] = (...args) => {
                // Call original console method
                originalConsole[method](...args);
                
                // Log to our debugger
                this.log(method.toUpperCase(), ...args);
            };
        });
        
        // Log page load
        this.log('DEBUG', 'Debugger initialized');
        this.log('PAGE', 'Page loaded', window.location.href);
        
        // Log AJAX requests
        this.interceptAjax();
        
        // Log fetch requests
        this.interceptFetch();
        
        // Log errors
        window.addEventListener('error', (event) => {
            this.log('ERROR', {
                message: event.message,
                source: event.filename,
                lineno: event.lineno,
                colno: event.colno,
                error: event.error?.stack
            });
        });
        
        // Log unhandled promise rejections
        window.addEventListener('unhandledrejection', (event) => {
            this.log('PROMISE_ERROR', {
                reason: event.reason?.message || event.reason,
                stack: event.reason?.stack
            });
        });
    },
    
    /**
     * Log a message
     */
    log(level = 'INFO', ...args) {
        if (!this.enabled) return;
        
        const timestamp = new Date().toISOString();
        const message = args.map(arg => {
            if (typeof arg === 'object') {
                try {
                    return JSON.stringify(arg);
                } catch (e) {
                    return String(arg);
                }
            }
            return arg;
        }).join(' ');
        
        const logEntry = `[${timestamp}] [${level}] ${message}`;
        
        // Add to logs array
        this.logs.push(logEntry);
        
        // Keep logs under max size
        if (this.logs.length > this.maxLogSize) {
            this.logs.shift();
        }
        
        // Send logs to server periodically
        if (this.logs.length % 10 === 0) {
            this.sendLogs();
        }
    },
    
    /**
     * Intercept AJAX requests
     */
    interceptAjax() {
        if (!window.XMLHttpRequest) return;
        
        const originalOpen = XMLHttpRequest.prototype.open;
        const originalSend = XMLHttpRequest.prototype.send;
        
        XMLHttpRequest.prototype.open = function(method, url) {
            this._method = method;
            this._url = url;
            this._startTime = new Date().getTime();
            
            // Log request start
            Debugger.log('XHR', `${method} ${url}`);
            
            return originalOpen.apply(this, arguments);
        };
        
        XMLHttpRequest.prototype.send = function(body) {
            if (body) {
                Debugger.log('XHR_BODY', `Request body:`, body);
            }
            
            this.addEventListener('load', function() {
                const duration = new Date().getTime() - this._startTime;
                Debugger.log('XHR', 
                    `${this._method} ${this._url} ` +
                    `${this.status} (${duration}ms)`
                );
                
                if (this.status >= 400) {
                    Debugger.log('XHR_ERROR', 
                        `${this._method} ${this._url} ` +
                        `Status: ${this.status} ${this.statusText}`
                    );
                }
                
                // Log response for debugging
                try {
                    const response = this.responseText;
                    if (response) {
                        Debugger.log('XHR_RESPONSE', 
                            `Response from ${this._url}:`, 
                            response.length > 500 ? response.substring(0, 500) + '...' : response
                        );
                    }
                } catch (e) {
                    Debugger.log('XHR_ERROR', 'Failed to parse response', e);
                }
            });
            
            this.addEventListener('error', function() {
                Debugger.log('XHR_ERROR', 
                    `${this._method} ${this._url} ` +
                    `Failed to load resource`
                );
            });
            
            return originalSend.apply(this, arguments);
        };
    },
    
    /**
     * Intercept fetch requests
     */
    interceptFetch() {
        if (!window.fetch) return;
        
        const originalFetch = window.fetch;
        
        window.fetch = function(resource, init = {}) {
            const startTime = new Date().getTime();
            const method = init.method || 'GET';
            const url = typeof resource === 'string' ? resource : resource.url;
            
            Debugger.log('FETCH', `${method} ${url}`);
            
            if (init.body) {
                Debugger.log('FETCH_BODY', `Request body:`, init.body);
            }
            
            return originalFetch(resource, init).then(response => {
                const duration = new Date().getTime() - startTime;
                
                // Clone the response so we can read it and still return it
                const clonedResponse = response.clone();
                
                Debugger.log('FETCH', 
                    `${method} ${url} ` +
                    `${response.status} (${duration}ms)`
                );
                
                if (response.status >= 400) {
                    Debugger.log('FETCH_ERROR', 
                        `${method} ${url} ` +
                        `Status: ${response.status} ${response.statusText}`
                    );
                }
                
                // Log response for debugging
                clonedResponse.text().then(text => {
                    if (text) {
                        Debugger.log('FETCH_RESPONSE', 
                            `Response from ${url}:`,
                            text.length > 500 ? text.substring(0, 500) + '...' : text
                        );
                    }
                }).catch(e => {
                    Debugger.log('FETCH_ERROR', 'Failed to read response', e);
                });
                
                return response;
            }).catch(error => {
                Debugger.log('FETCH_ERROR', 
                    `${method} ${url} ` +
                    `Failed to fetch`, error
                );
                throw error;
            });
        };
    },
    
    /**
     * Send logs to the server
     */
    sendLogs() {
        if (this.logs.length === 0) return;
        
        const logsToSend = [...this.logs];
        this.logs = [];
        
        // Send logs to the server
        navigator.sendBeacon && navigator.sendBeacon(
            'log.php',
            JSON.stringify({
                logs: logsToSend,
                url: window.location.href,
                timestamp: new Date().toISOString()
            })
        );
    },
    
    /**
     * Export logs for debugging
     */
    exportLogs() {
        return this.logs.join('\n');
    }
};

// Initialize the debugger when the DOM is loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => Debugger.init());
} else {
    Debugger.init();
}

// Make debugger available globally
window.Debugger = Debugger;
