---
# Optimized Execution Module Requirements
File: `custom_exec_requirements.md`
---

## 1. Feature Objective

Introduce an **Optimized Execution Module** within the TestLink application.

This module will serve as a high-performance alternative to the legacy `execNavigator.php` and `execDashboard.php` interfaces. It is specifically designed to support large-scale test suites (1000+ test cases) where the current implementation suffers from rendering delays, excessive server load, and frequent full-page refreshes.

The goal is simple:
- Reduce latency
- Eliminate unnecessary reloads
- Provide a fluid execution workflow

---

## 2. Core Enhancements

### 2.1 Asynchronous Tree Navigation (Lazy Loading)

Instead of rendering the entire test case hierarchy on initial page load:

- Load only root nodes initially
- Fetch child nodes dynamically via AJAX when expanded
- Cache expanded nodes to prevent redundant queries
- Display lightweight loading indicators during fetch

**Expected Impact:**
- Massive reduction in initial payload size
- Faster first paint
- Scalable handling of deeply nested test structures

---

### 2.2 Unified Execution Workspace (SPA-Style)

Merge Navigator and Dashboard into a single-page execution interface:

- No full page reloads
- Dynamic content swapping via AJAX
- Execution updates handled asynchronously
- Browser history preserved via pushState (optional enhancement)

**Benefits:**
- Smoother user experience
- Reduced server rendering overhead
- Improved workflow continuity for testers

---

### 2.3 Real-Time Status Tracking

Implement a dynamic execution statistics header displaying:

- Pass
- Fail
- Blocked
- Not Run

These counters will:
- Update instantly via AJAX after execution submission
- Reflect accurate state for current Test Plan and Build
- Prevent manual refresh dependency

---

### 2.4 Reactive UI Components

#### Color-Coded Execution States
- Green → Pass
- Red → Fail
- Orange → Blocked
- Grey → Not Run

Applied to:
- Tree nodes
- Active test case header
- Execution dashboard indicators

#### Quick-Action Buttons
Large, accessible buttons embedded directly in the execution panel:

- Pass
- Fail
- Block
- Reset (optional)

Actions trigger:
- Immediate database write
- UI state update without reload
- Header counter recalculation

---

## 3. Technical Integration Plan

### 3.1 Backend Strategy

#### Option A: PHP + AJAX (Native Extension)
- Create new lightweight PHP controllers
- Return JSON responses instead of HTML blocks
- Reuse existing TestLink permission logic
- Minimize intrusion into legacy system

Best for:
- Lower architectural complexity
- Faster implementation timeline

---

#### Option B: Python Microservice (FastAPI Sidecar)

Deploy a lightweight FastAPI container that:

- Connects directly to the TestLink database
- Handles heavy execution read/write operations
- Exposes REST endpoints consumed by frontend
- Communicates via reverse proxy

Advantages:
- Clear separation of concerns
- Scalable under heavy load
- Easier performance tuning and async handling

Trade-off:
- Slightly increased deployment complexity

---

### 3.2 Frontend Stack

- JavaScript (ES6+)
- Fetch API or lightweight AJAX wrapper
- Modular component-based DOM rendering
- CSS-based status coloring for minimal overhead

Design principles:
- No heavy frameworks required
- Maintain compatibility with existing TestLink layout
- Low rendering cost and fast repaint cycles

---

### 3.3 Data Consistency Strategy

All execution actions must write directly to native TestLink tables:

- `executions`
- `nodes_hierarchy`
- `tcversions`
- Related execution tracking tables

This guarantees:
- Full synchronization with legacy views
- No duplicate logic
- Seamless rollback to classic interface if needed

---

## 4. Proposed Single-Screen Layout

| Component | Functionality |
|------------|--------------|
| **Left Pane: Lazy Tree** | Expand-on-demand navigation tree with real-time status icons |
| **Top Bar: Live Stats** | Execution counters + build selector + legacy speed toggle |
| **Center Pane: Active Case** | Summary, Steps, Expected Results, Attachments |
| **Right/Bottom Pane: Execution Area** | Test data input, notes field, quick-action execution buttons |

Optional Enhancements:
- Keyboard shortcuts for Pass/Fail/Block
- Execution time tracking
- Auto-focus navigation to next "Not Run" case

---

## 5. Performance Targets (Success Metrics)

### 5.1 Load Performance
- Initial load time reduced by >70% for test plans with 1000+ test cases
- Tree rendering time reduced to <500ms

### 5.2 Execution Latency
- Case switching latency <200ms
- Execution submission roundtrip <150ms

### 5.3 Stability
- Zero data inconsistency with legacy execution view
- Graceful fallback to legacy interface if AJAX fails

---

## 6. Risk Considerations

- Database write conflicts in concurrent execution scenarios
- Permission validation consistency across services
- Session management alignment if microservice architecture is adopted
- Long-term maintainability if partially decoupled from legacy codebase

Mitigation:
- Transaction-level validation
- Reuse of existing authentication layer
- Structured API contracts

---

## 7. Future Scalability Opportunities

- Execution filtering by status
- Bulk execution update mode
- Real-time collaboration indicators
- WebSocket-based live team dashboard
- Execution analytics dashboard integration

---

## Conclusion

The Optimized Execution Module transforms the execution workflow from a page-refresh-heavy legacy experience into a responsive, scalable, modern execution environment.

It focuses on:

- Speed  
- Fluidity  
- Accuracy  
- Scalability  

While preserving full compatibility with TestLink’s existing data model.

