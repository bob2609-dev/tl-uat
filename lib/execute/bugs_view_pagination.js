// Bugs View Pagination and AJAX JavaScript Functions
// This file contains all JavaScript functionality for bugs_view.php pagination and AJAX

// Function to update cell with error message
function updateCellWithError(selector, message) {
    $(selector).html('<span class="status-error">' + message + '</span>');
}

// Function to update row with Redmine status data
function updateRowWithRedmineStatus(bugId, statusData) {
    const cell = $('.redmine-status-cell[data-bug-id="' + bugId + '"]');
    if (cell.length === 0) {
        console.warn('No cell found for bug ID:', bugId);
        return;
    }
    
    let statusHtml = '';
    if (statusData.error) {
        statusHtml = '<span class="status-error">' + statusData.status + '</span>' +
                    ' <button class="recheck-btn" onclick="recheckBugStatus(' + bugId + ')" title="Retry loading status">♾️</button>';
    } else {
        statusHtml = '<span class="status-' + statusData.status.toLowerCase().replace(/\s+/g, '-') + '">' + statusData.status + '</span>';
    }
    
    cell.html(statusHtml);
}

// Function to recheck a single bug's Redmine status (called by recheck button)
function recheckBugStatus(bugId) {
    console.log(`🔄 Rechecking status for bug ${bugId}`);
    
    // Clear cached status
    delete redmineStatusCache[bugId];
    
    // Set loading indicator
    const cell = $('.redmine-status-cell[data-bug-id="' + bugId + '"]');
    cell.html('<span class="status-loading">Loading...</span>');
    
    // Load status for this single bug
    loadRedmineStatusSequentially([bugId], 0);
}

// Mock function to demonstrate working pagination while fixing JavaScript issues
function loadMockRedmineStatuses(bugIds) {
    const mockStatuses = ['Open', 'In Progress', 'Resolved', 'Closed', 'New', 'Feedback'];
    
    bugIds.forEach(function(bugId, index) {
        setTimeout(function() {
            const randomStatus = mockStatuses[Math.floor(Math.random() * mockStatuses.length)];
            const mockData = {
                id: bugId,
                status: randomStatus,
                priority: 'Normal',
                assigned_to: 'Developer',
                updated_on: new Date().toISOString(),
                error: false
            };
            
            redmineStatusCache[bugId] = mockData;
            updateRowWithRedmineStatus(bugId, mockData);
        }, index * 300); // 300ms delay between updates for visual effect
    });
}

// Function to load Redmine statuses one by one (AJAX-only with console logging)
function loadRedmineStatusSequentially(bugIds, index) {
    if (index >= bugIds.length) {
        console.log('✅ All Redmine statuses loaded successfully');
        return;
    }
    
    const bugId = bugIds[index];
    console.log(`🔄 Loading Redmine status for bug ${bugId} (${index + 1}/${bugIds.length})`);
    
    // Skip if already cached
    if (redmineStatusCache[bugId]) {
        console.log(`💾 Using cached status for bug ${bugId}:`, redmineStatusCache[bugId]);
        updateRowWithRedmineStatus(bugId, redmineStatusCache[bugId]);
        loadRedmineStatusSequentially(bugIds, index + 1);
        return;
    }
    
    // Make AJAX call to our PHP endpoint (bypasses CORS and SSL issues)
    const requestUrl = 'ajax_redmine_status.php';
    const requestData = {
        bug_ids: [bugId]
    };
    
    console.log(`🌐 Making AJAX request to PHP endpoint: ${requestUrl}`);
    console.log(`📤 Request data:`, requestData);
    
    $.ajax({
        url: requestUrl,
        method: 'POST',
        data: JSON.stringify(requestData),
        contentType: 'application/json',
        dataType: 'json',
        timeout: 15000,
        headers: {
            'X-CSRF-Name': (typeof window.CSRFNameAjax !== 'undefined') ? window.CSRFNameAjax : '',
            'X-CSRF-Token': (typeof window.CSRFTokenAjax !== 'undefined') ? window.CSRFTokenAjax : ''
        },
        success: function(response) {
            console.log(`✅ PHP endpoint success for bug ${bugId}:`, response);
            
            // Rotate CSRF tokens if server provided new ones
            if (response && response.next_csrf_name && response.next_csrf_token) {
                window.CSRFNameAjax = response.next_csrf_name;
                window.CSRFTokenAjax = response.next_csrf_token;
                console.log('🔐 CSRF tokens rotated for next request');
            }
            
            if (response.success && response.data && response.data[bugId]) {
                const statusData = response.data[bugId];
                console.log(`📊 Processed status data for bug ${bugId}:`, statusData);
                
                redmineStatusCache[bugId] = statusData;
                updateRowWithRedmineStatus(bugId, statusData);
            } else {
                console.error(`⚠️ No data returned for bug ${bugId}:`, response);
                const errorData = {
                    id: bugId,
                    status: 'No Data',
                    priority: 'Unknown',
                    assigned_to: 'Unknown',
                    updated_on: null,
                    error: true
                };
                redmineStatusCache[bugId] = errorData;
                updateRowWithRedmineStatus(bugId, errorData);
            }
            
            // Load next status after a short delay
            setTimeout(function() {
                loadRedmineStatusSequentially(bugIds, index + 1);
            }, 300);
        },
        error: function(xhr, status, error) {
            console.error(`❌ PHP endpoint error for bug ${bugId}:`, {
                status: status,
                error: error,
                responseText: xhr.responseText,
                statusCode: xhr.status
            });
            
            const errorData = {
                id: bugId,
                status: `Error (${xhr.status})`,
                priority: 'Unknown',
                assigned_to: 'Unknown',
                updated_on: null,
                error: true
            };
            
            redmineStatusCache[bugId] = errorData;
            updateRowWithRedmineStatus(bugId, errorData);
            
            // Continue with next bug even on error
            setTimeout(function() {
                loadRedmineStatusSequentially(bugIds, index + 1);
            }, 300);
        }
    });
}

// Function to load Redmine statuses for visible bugs
function loadRedmineStatusesForVisibleBugs() {
    console.log('🔍 loadRedmineStatusesForVisibleBugs() called');
    
    // Try different selectors to find table rows
    const allRows = $('table tbody tr');
    const visibleRows = $('table tbody tr:visible');
    const bugRows = $('.bug-row');
    const bugRowsVisible = $('.bug-row:visible');
    
    console.log('📊 Row detection:', {
        'All tbody rows': allRows.length,
        'Visible tbody rows': visibleRows.length,
        'Bug rows (.bug-row)': bugRows.length,
        'Visible bug rows': bugRowsVisible.length
    });
    
    // Use the most appropriate selector
    const targetRows = visibleRows.length > 0 ? visibleRows : allRows;
    console.log('🎯 Using rows:', targetRows.length);
    
    const bugIds = [];
    const debugInfo = [];
    
    targetRows.each(function(index) {
        const row = $(this);
        
        // Skip details rows
        if (row.hasClass('details-row')) {
            return;
        }
        
        // Try different methods to get bug ID
        const redmineCell = row.find('.redmine-status-cell');
        const bugIdFromCell = redmineCell.data('bug-id');
        const bugIdFromAttr = redmineCell.attr('data-bug-id');
        
        // Also try to find bug ID in other cells
        const allCells = row.find('td');
        let bugIdFromText = null;
        allCells.each(function() {
            const cellText = $(this).text().trim();
            if (/^\d+$/.test(cellText) && cellText.length >= 3) {
                bugIdFromText = cellText;
                return false; // break
            }
        });
        
        const debugRow = {
            index: index,
            hasRedmineCell: redmineCell.length > 0,
            bugIdFromCell: bugIdFromCell,
            bugIdFromAttr: bugIdFromAttr,
            bugIdFromText: bugIdFromText,
            cellCount: allCells.length,
            rowHtml: row.html().substring(0, 200) + '...'
        };
        
        debugInfo.push(debugRow);
        
        // Use the first valid bug ID we find
        const bugId = bugIdFromCell || bugIdFromAttr || bugIdFromText;
        
        if (bugId && !redmineStatusCache[bugId]) {
            bugIds.push(bugId);
            console.log(`✅ Found bug ID: ${bugId} (from ${bugIdFromCell ? 'cell-data' : bugIdFromAttr ? 'attr' : 'text'})`);
        } else if (bugId) {
            console.log(`💾 Bug ID ${bugId} already cached`);
        }
    });
    
    console.log('🔍 Debug info for first 3 rows:', debugInfo.slice(0, 3));
    console.log('🎯 Found bug IDs:', bugIds);
    
    if (bugIds.length === 0) {
        console.log('⚠️ No bug IDs found - no AJAX calls will be made');
        return;
    }
    
    console.log(`🚀 Starting AJAX calls for ${bugIds.length} bugs`);
    
    // Set loading indicators
    bugIds.forEach(function(bugId) {
        $('.redmine-status-cell[data-bug-id="' + bugId + '"]').html('<span class="status-loading">Loading...</span>');
    });
    
    // Use real AJAX calls to Redmine API with console logging
    loadRedmineStatusSequentially(bugIds, 0);
}

// Function to setup search functionality
function setupSearch() {
    // Enhanced search functionality that works with pagination
    function performSearch() {
        const searchTerm = $('#search-input').val().toLowerCase();
        
        if (searchTerm === '') {
            filteredRows = allRows.slice();
        } else {
            filteredRows = allRows.filter(function(row) {
                const rowText = $(row).text().toLowerCase();
                return rowText.includes(searchTerm);
            });
        }
        
        currentPage = 1;
        initializePagination();
    }
    
    // Add search input if it doesn't exist
    if ($('#search-input').length === 0) {
        $('.search-form').append('<div><label>Quick Search: <input type="text" id="search-input" placeholder="Search in table..."></label></div>');
    }
    
    // Bind search events
    $('#search-input').on('keyup', performSearch);
}

// Function to initialize pagination (server-side)
function initializePagination() {
    console.log('🔧 initializePagination() called');
    console.log('📊 Pagination info:', { totalBugs, pageSize, currentPage });
    
    // All rows are already visible from server-side pagination
    // Just update pagination controls and load Redmine statuses
    const totalPages = Math.ceil(totalBugs / pageSize);
    console.log('📄 Total pages calculated:', totalPages);
    
    updatePaginationControls(totalPages);
    
    // Load Redmine statuses for visible bugs
    console.log('🔄 About to call loadRedmineStatusesForVisibleBugs()');
    loadRedmineStatusesForVisibleBugs();
}

// Function to update pagination controls
function updatePaginationControls(totalPages) {
    let paginationHtml = '<div class="pagination-controls">';
    paginationHtml += '<div class="pagination-info">Page ' + currentPage + ' of ' + totalPages + ' (' + filteredRows.length + ' bugs)</div>';
    
    // Page size selector
    paginationHtml += '<div class="page-size-selector">';
    paginationHtml += 'Show: ';
    paginationHtml += '<select id="page-size-select">';
    paginationHtml += '<option value="25"' + (pageSize === 25 ? ' selected' : '') + '>25</option>';
    paginationHtml += '<option value="50"' + (pageSize === 50 ? ' selected' : '') + '>50</option>';
    paginationHtml += '<option value="100"' + (pageSize === 100 ? ' selected' : '') + '>100</option>';
    paginationHtml += '</select> bugs per page';
    paginationHtml += '</div>';
    
    // Navigation buttons
    paginationHtml += '<div class="pagination-buttons">';
    
    // Previous button
    if (currentPage > 1) {
        paginationHtml += '<button onclick="goToPage(' + (currentPage - 1) + ')">Previous</button>';
    }
    
    // Page numbers
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        if (i === currentPage) {
            paginationHtml += '<button class="current-page">' + i + '</button>';
        } else {
            paginationHtml += '<button onclick="goToPage(' + i + ')">' + i + '</button>';
        }
    }
    
    // Next button
    if (currentPage < totalPages) {
        paginationHtml += '<button onclick="goToPage(' + (currentPage + 1) + ')">Next</button>';
    }
    
    paginationHtml += '</div>';
    paginationHtml += '</div>';
    
    // Update or create pagination container
    if ($('.pagination-controls').length > 0) {
        $('.pagination-controls').replaceWith(paginationHtml);
    } else {
        $('#bugs-table').after(paginationHtml);
    }
    
    // Bind page size change event
    $('#page-size-select').on('change', function() {
        const newPageSize = parseInt($(this).val());
        const url = new URL(window.location);
        url.searchParams.set('page', 1); // Reset to first page
        url.searchParams.set('pageSize', newPageSize);
        window.location.href = url.toString();
    });
}

// Function to go to specific page (server-side)
function goToPage(page) {
    const url = new URL(window.location);
    url.searchParams.set('page', page);
    url.searchParams.set('pageSize', pageSize);
    window.location.href = url.toString();
}

// Function to change page (called by HTML buttons)
function changePage(direction) {
    const urlParams = new URLSearchParams(window.location.search);
    const currentPage = parseInt(urlParams.get('page')) || 1;
    const newPage = currentPage + direction;
    
    if (newPage >= 1) {
        goToPage(newPage);
    }
}

// Function to change page size (called by HTML select)
function changePageSize() {
    const newPageSize = parseInt($('#page-size').val());
    const url = new URL(window.location);
    url.searchParams.set('page', 1); // Reset to first page
    url.searchParams.set('pageSize', newPageSize);
    window.location.href = url.toString();
}

// Toggle details function (existing)
function toggleDetails(executionId) {
    $('#details_' + executionId).toggle();
}
