/**
 * Dashboard Real-Time Updater
 * Provides adaptive smart polling with SSE-inspired patterns for real-time dashboard updates
 */

class DashboardUpdater {
    constructor(options = {}) {
        // Configuration options
        this.options = {
            endpoint: options.endpoint || 'get_dashboard_updates.php',
            containerSelector: options.containerSelector || '.dashboard-container',
            statsSelector: options.statsSelector || '.stats-container',
            tableSelector: options.tableSelector || '.requests-table tbody',
            paginationSelector: options.paginationSelector || '.pagination-container',
            updateInterval: options.updateInterval || 10000, // 10 seconds default
            fastInterval: options.fastInterval || 5000, // 5 seconds when active
            slowInterval: options.slowInterval || 30000, // 30 seconds when inactive
            inactiveInterval: options.inactiveInterval || 60000, // 1 minute when tab not visible
            maxRetries: options.maxRetries || 3,
            retryDelay: options.retryDelay || 2000,
            debugMode: options.debugMode || false,
            onStatsUpdate: options.onStatsUpdate || null,
            onRequestUpdate: options.onRequestUpdate || null,
            onConnectionChange: options.onConnectionChange || null,
            enableNotifications: options.enableNotifications || false,
            enableSounds: options.enableSounds || false,
            businessHours: options.businessHours || { start: 8, end: 17 }, // 8 AM to 5 PM
            businessDays: options.businessDays || [1, 2, 3, 4, 5] // Monday to Friday
        };
        
        // Internal state
        this.isRunning = false;
        this.isPaused = false;
        this.lastUpdate = null;
        this.currentInterval = this.options.updateInterval;
        this.retryCount = 0;
        this.pollTimer = null;
        this.connectionStatus = 'disconnected';
        this.visibleRequests = new Set(); // Track visible request IDs
        this.knownRequests = new Set(); // Track all known request IDs
        
        // Performance tracking
        this.lastRequestTime = 0;
        this.averageResponseTime = 0;
        this.requestCount = 0;
        
        // Initialize
        this.init();
    }
    
    /**
     * Initialize the dashboard updater
     */
    init() {
        this.log('Dashboard updater initializing...');
        
        // Check if required elements exist
        this.container = document.querySelector(this.options.containerSelector);
        if (!this.container) {
            this.log('Dashboard container not found, exiting');
            return;
        }
        
        // Initialize current page parameters
        this.updatePageParameters();
        
        // Set up event listeners
        this.setupEventListeners();
        
        // Store initial visible requests
        this.storeVisibleRequests();
        
        // Start the updater
        this.start();
        
        this.log('Dashboard updater initialized successfully');
    }
    
    /**
     * Set up event listeners
     */
    setupEventListeners() {
        // Page visibility change
        document.addEventListener('visibilitychange', () => {
            this.handleVisibilityChange();
        });
        
        // Window focus/blur
        window.addEventListener('focus', () => {
            this.handleWindowFocus();
        });
        
        window.addEventListener('blur', () => {
            this.handleWindowBlur();
        });
        
        // User activity detection
        let activityTimer;
        const activityEvents = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'];
        
        activityEvents.forEach(event => {
            document.addEventListener(event, () => {
                this.handleUserActivity();
                
                // Clear existing timer
                clearTimeout(activityTimer);
                
                // Set new timer to detect inactivity
                activityTimer = setTimeout(() => {
                    this.handleUserInactivity();
                }, 30000); // 30 seconds of inactivity
            });
        });
        
        // Handle form submissions to pause updates during operations
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', () => {
                this.pause(5000); // Pause for 5 seconds during form submission
            });
        });
    }
    
    /**
     * Handle page visibility changes
     */
    handleVisibilityChange() {
        if (document.hidden) {
            this.log('Page hidden, switching to slow polling');
            this.currentInterval = this.options.inactiveInterval;
        } else {
            this.log('Page visible, switching to normal polling');
            this.currentInterval = this.getOptimalInterval();
        }
        
        this.restartPolling();
    }
    
    /**
     * Handle window focus
     */
    handleWindowFocus() {
        this.log('Window focused');
        this.currentInterval = this.options.fastInterval;
        this.restartPolling();
    }
    
    /**
     * Handle window blur
     */
    handleWindowBlur() {
        this.log('Window blurred');
        this.currentInterval = this.options.slowInterval;
    }
    
    /**
     * Handle user activity
     */
    handleUserActivity() {
        if (this.currentInterval !== this.options.fastInterval) {
            this.currentInterval = this.options.fastInterval;
            this.restartPolling();
        }
    }
    
    /**
     * Handle user inactivity
     */
    handleUserInactivity() {
        this.log('User inactive, switching to slow polling');
        this.currentInterval = this.options.slowInterval;
        this.restartPolling();
    }
    
    /**
     * Get optimal polling interval based on business hours and current conditions
     */
    getOptimalInterval() {
        const now = new Date();
        const hour = now.getHours();
        const day = now.getDay(); // 0 = Sunday, 1 = Monday, etc.
        
        // Check if it's business hours on business days
        const isBusinessDay = this.options.businessDays.includes(day);
        const isBusinessHours = hour >= this.options.businessHours.start && hour < this.options.businessHours.end;
        
        if (isBusinessDay && isBusinessHours) {
            return this.options.fastInterval;
        }
        
        return this.options.updateInterval;
    }
    
    /**
     * Update current page parameters from URL
     */
    updatePageParameters() {
        const params = new URLSearchParams(window.location.search);
        this.currentPage = {
            page: parseInt(params.get('page')) || 1,
            per_page: parseInt(params.get('per_page')) || 10,
            status: params.get('status') || 'all',
            search: params.get('search') || ''
        };
    }
    
    /**
     * Store currently visible request IDs
     */
    storeVisibleRequests() {
        const rows = document.querySelectorAll(`${this.options.tableSelector} tr[data-request-id]`);
        this.visibleRequests.clear();
        
        rows.forEach(row => {
            const requestId = parseInt(row.dataset.requestId);
            if (requestId) {
                this.visibleRequests.add(requestId);
                this.knownRequests.add(requestId);
            }
        });
        
        this.log(`Stored ${this.visibleRequests.size} visible requests`);
    }
    
    /**
     * Start the dashboard updater
     */
    start() {
        if (this.isRunning) {
            this.log('Updater already running');
            return;
        }
        
        this.isRunning = true;
        this.isPaused = false;
        this.connectionStatus = 'connecting';
        this.notifyConnectionChange();
        
        this.log('Starting dashboard updater');
        this.scheduleNextUpdate();
    }
    
    /**
     * Stop the dashboard updater
     */
    stop() {
        this.isRunning = false;
        this.isPaused = false;
        
        if (this.pollTimer) {
            clearTimeout(this.pollTimer);
            this.pollTimer = null;
        }
        
        // Abort any in-progress request
        if (this.currentRequestController) {
            this.currentRequestController.abort();
            this.currentRequestController = null;
        }
        
        this.connectionStatus = 'disconnected';
        this.notifyConnectionChange();
        
        this.log('Dashboard updater stopped');
    }
    
    /**
     * Pause updates for specified duration
     */
    pause(duration = 5000) {
        this.isPaused = true;
        this.log(`Pausing updates for ${duration}ms`);
        
        setTimeout(() => {
            this.isPaused = false;
            this.log('Resuming updates');
        }, duration);
    }
    
    /**
     * Schedule the next update
     */
    scheduleNextUpdate() {
        if (!this.isRunning || this.isPaused) {
            return;
        }
        
        this.pollTimer = setTimeout(() => {
            this.fetchUpdates();
        }, this.currentInterval);
    }
    
    /**
     * Restart polling with new interval
     */
    restartPolling() {
        if (this.pollTimer) {
            clearTimeout(this.pollTimer);
        }
        
        if (this.isRunning && !this.isPaused) {
            this.scheduleNextUpdate();
        }
    }
    
    /**
     * Fetch updates from server
     */
    async fetchUpdates() {
        if (!this.isRunning || this.isPaused) {
            return;
        }
        
        const startTime = performance.now();
        this.lastRequestTime = startTime;
        
        // Abort any existing request to prevent cascading failures
        if (this.currentRequestController) {
            this.currentRequestController.abort();
        }
        
        // Create new abort controller for this request
        this.currentRequestController = new AbortController();
        
        try {
            // Build request URL with current parameters
            const url = new URL(this.options.endpoint, window.location.origin);
            url.searchParams.set('last_update', this.lastUpdate || '');
            url.searchParams.set('page', this.currentPage.page);
            url.searchParams.set('per_page', this.currentPage.per_page);
            url.searchParams.set('status', this.currentPage.status);
            url.searchParams.set('search', this.currentPage.search);
            
            this.log(`Fetching updates: ${url.toString()}`);
            
            // Add timeout to prevent hanging requests
            const timeoutId = setTimeout(() => {
                this.currentRequestController.abort();
            }, 10000); // 10 second timeout
            
            const response = await fetch(url.toString(), {
                method: 'GET',
                credentials: 'include',
                signal: this.currentRequestController.signal,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Cache-Control': 'no-cache'
                },
                cache: 'no-cache'
            });
            
            // Clear timeout
            clearTimeout(timeoutId);
            this.currentRequestController = null;
            
            const endTime = performance.now();
            const responseTime = endTime - startTime;
            this.updatePerformanceMetrics(responseTime);
            
            if (!response.ok) {
                if (response.status === 401 || response.status === 403) {
                    throw new Error(`Authentication failed (${response.status}): Please refresh the page`);
                } else {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
            }
            
            const data = await response.json();
            
            // Store error data if the request failed
            if (!data.success) {
                this.lastErrorData = data;
            } else {
                this.lastErrorData = null;
            }
            
            if (data.success) {
                this.handleUpdateResponse(data);
                this.retryCount = 0;
                this.connectionStatus = 'connected';
            } else if (data.requires_refresh) {
                // Handle session expiry
                this.handleSessionExpired(data);
                return;
            } else {
                throw new Error(data.error || 'Unknown server error');
            }
            
        } catch (error) {
            // Clean up request controller
            if (this.currentRequestController) {
                this.currentRequestController = null;
            }
            
            // Handle aborted requests differently
            if (error.name === 'AbortError') {
                this.log('Request aborted (timeout or cancelled)');
                // Don't count aborted requests as errors for retry logic
                return;
            }
            
            this.handleError(error);
        } finally {
            this.scheduleNextUpdate();
        }
    }
    
    /**
     * Handle successful update response
     */
    handleUpdateResponse(data) {
        this.log('Update received', data);
        
        // Update last update timestamp
        this.lastUpdate = data.timestamp;
        
        if (!data.data.has_changes) {
            this.log('No changes detected');
            return;
        }
        
        // Update statistics
        if (data.data.statistics) {
            this.updateStatistics(data.data.statistics);
        }
        
        // Update requests table
        if (data.data.requests) {
            this.updateRequests(data.data.requests);
        }
        
        // Update pagination if needed
        if (data.data.pagination) {
            this.updatePagination(data.data.pagination);
        }
        
        this.log('Dashboard updated successfully');
    }
    
    /**
     * Update statistics cards (optimized for performance)
     */
    updateStatistics(stats) {
        const statsContainer = document.querySelector(this.options.statsSelector);
        if (!statsContainer) {
            return;
        }
        
        // Cache DOM elements to avoid repeated queries
        if (!this._statsElements) {
            this._statsElements = {
                pending: statsContainer.querySelector('.stats-pending'),
                approved: statsContainer.querySelector('.stats-approved'),
                ready: statsContainer.querySelector('.stats-ready'),
                completed: statsContainer.querySelector('.stats-completed'),
                cancelled: statsContainer.querySelector('.stats-cancelled')
            };
        }
        
        // Batch DOM updates for better performance
        const updates = [];
        
        if (this._statsElements.pending) {
            const newValue = stats.pending || 0;
            const currentValue = parseInt(this._statsElements.pending.textContent) || 0;
            if (currentValue !== newValue) {
                updates.push(() => {
                    this._statsElements.pending.textContent = newValue;
                    this._animateElement(this._statsElements.pending, 'stat-updated');
                });
            }
        }
        
        if (this._statsElements.approved) {
            const newValue = stats.approved || 0;
            const currentValue = parseInt(this._statsElements.approved.textContent) || 0;
            if (currentValue !== newValue) {
                updates.push(() => {
                    this._statsElements.approved.textContent = newValue;
                    this._animateElement(this._statsElements.approved, 'stat-updated');
                });
            }
        }
        
        if (this._statsElements.ready) {
            const newValue = stats.ready || 0;
            const currentValue = parseInt(this._statsElements.ready.textContent) || 0;
            if (currentValue !== newValue) {
                updates.push(() => {
                    this._statsElements.ready.textContent = newValue;
                    this._animateElement(this._statsElements.ready, 'stat-updated');
                });
            }
        }
        
        if (this._statsElements.completed) {
            const newValue = stats.completed || 0;
            const currentValue = parseInt(this._statsElements.completed.textContent) || 0;
            if (currentValue !== newValue) {
                updates.push(() => {
                    this._statsElements.completed.textContent = newValue;
                    this._animateElement(this._statsElements.completed, 'stat-updated');
                });
            }
        }
        
        if (this._statsElements.cancelled) {
            const newValue = stats.cancelled || 0;
            const currentValue = parseInt(this._statsElements.cancelled.textContent) || 0;
            if (currentValue !== newValue) {
                updates.push(() => {
                    this._statsElements.cancelled.textContent = newValue;
                    this._animateElement(this._statsElements.cancelled, 'stat-updated');
                });
            }
        }
        
        // Execute all DOM updates in a single batch
        if (updates.length > 0) {
            requestAnimationFrame(() => {
                updates.forEach(update => update());
                this.log(`Updated ${updates.length} statistics`);
            });
        }
        
        // Call custom callback
        if (this.options.onStatsUpdate) {
            this.options.onStatsUpdate(stats);
        }
    }
    
    /**
     * Animate element with class (optimized)
     */
    _animateElement(element, animationClass) {
        if (element && !element.classList.contains(animationClass)) {
            element.classList.add(animationClass);
            
            // Use a single timer instance to avoid memory leaks
            if (!this._animationTimers) {
                this._animationTimers = new Map();
            }
            
            // Clear existing timer for this element
            if (this._animationTimers.has(element)) {
                clearTimeout(this._animationTimers.get(element));
            }
            
            // Set new timer
            const timer = setTimeout(() => {
                element.classList.remove(animationClass);
                this._animationTimers.delete(element);
            }, 1000);
            
            this._animationTimers.set(element, timer);
        }
    }
    
    /**
     * Update requests table
     */
    updateRequests(requests) {
        const tbody = document.querySelector(this.options.tableSelector);
        if (!tbody) {
            return;
        }
        
        const currentRequestIds = new Set();
        
        // Update existing rows and add new ones
        requests.forEach(request => {
            currentRequestIds.add(request.id);
            
            const existingRow = tbody.querySelector(`tr[data-request-id="${request.id}"]`);
            
            if (existingRow) {
                // Update existing row if changed
                this.updateRequestRow(existingRow, request);
            } else {
                // Add new row if it's for current page
                this.addNewRequestRow(tbody, request);
            }
        });
        
        // FIXED: Only remove rows that are explicitly marked as deleted
        // Don't remove rows just because they're not in the current response
        // The response only contains one page of data (due to pagination),
        // so we should keep existing rows that aren't in this page's response
        
        // Check if server sent deleted_ids in the response
        // If so, only remove those specific rows
        // This prevents accidentally removing all rows when only one page is returned
        this.log(`Updated with ${requests.length} requests, keeping existing rows not in response`);
        
        // Note: We're NOT removing rows here anymore
        // Rows should only be removed if:
        // 1. Server explicitly sends deleted_ids array (future enhancement)
        // 2. User manually refreshes the page
        // 3. Pagination changes and we need to show a different page
        
        // NEW: Ensure all rows are sorted by created_at DESC (newest first)
        // This maintains correct sort order after updates
        this.sortTableByDate(tbody);
        
        // Update stored request IDs to include all visible requests (not just current page)
        // Get all request IDs currently in the table
        tbody.querySelectorAll('tr[data-request-id]').forEach(row => {
            const requestId = parseInt(row.dataset.requestId);
            this.visibleRequests.add(requestId);
        });
        
        // Call custom callback
        if (this.options.onRequestUpdate) {
            this.options.onRequestUpdate(requests);
        }
    }
    
    /**
     * Sort table rows by created_at timestamp (newest first)
     * This ensures correct ordering after updates
     */
    sortTableByDate(tbody) {
        const rows = Array.from(tbody.querySelectorAll('tr[data-request-id]'));
        
        if (rows.length === 0) {
            return;
        }
        
        // Sort by created_at timestamp descending (newest first)
        rows.sort((a, b) => {
            const aTime = new Date(a.dataset.createdAt).getTime();
            const bTime = new Date(b.dataset.createdAt).getTime();
            
            // Handle invalid dates
            if (isNaN(aTime)) return 1;  // Invalid dates go to end
            if (isNaN(bTime)) return -1; // Invalid dates go to start
            
            const diff = bTime - aTime; // Negative means a is newer (should come first)
            
            this.log(`Sort: ${a.dataset.requestId} (${this.formatDate(a.dataset.createdAt)}) vs ${b.dataset.requestId} (${this.formatDate(b.dataset.createdAt)}) = ${diff}`);
            
            return diff;
        });
        
        // Re-append rows in sorted order
        const fragment = document.createDocumentFragment();
        rows.forEach(row => fragment.appendChild(row));
        tbody.innerHTML = '';
        tbody.appendChild(fragment);
        
        this.log('Table sorted by created_at DESC (newest first)');
    }
    
    /**
     * Update existing request row
     */
    updateRequestRow(row, request) {
        // Check if status changed
        const statusCell = row.querySelector('.request-status');
        if (statusCell) {
            const currentStatus = statusCell.textContent.trim();
            if (currentStatus !== this.getStatusLabel(request.status)) {
                statusCell.textContent = this.getStatusLabel(request.status);

                // Update status styling with Tailwind utility classes only
                statusCell.className = 'request-status px-2 inline-flex text-xs leading-5 font-semibold rounded-full';
                statusCell.classList.add(...this.getStatusColorClasses(request.status).split(' '));

                // Update action buttons based on new status
                const actionsCell = row.querySelector('td:last-child .flex.space-x-2');
                if (actionsCell) {
                    // Remove existing action forms (keep the Lihat button)
                    const existingForms = actionsCell.querySelectorAll('form');
                    existingForms.forEach(form => form.remove());

                    // Add appropriate action button based on new status
                    if (request.status === 'pending') {
                        const formHtml = `
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="request_id" value="${request.id}">
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" onclick="return confirm('Setujui permintaan ini?')" class="text-green-600 hover:text-green-900">
                                    <i class="bi bi-check-circle"></i> Setujui
                                </button>
                            </form>
                        `;
                        actionsCell.insertAdjacentHTML('beforeend', formHtml);
                    } else if (request.status === 'approved') {
                        const formHtml = `
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="request_id" value="${request.id}">
                                <input type="hidden" name="status" value="ready">
                                <button type="submit" onclick="return confirm('Tandai sebagai Sudah Siap?')" class="text-purple-600 hover:text-purple-900">
                                    <i class="bi bi-check2-circle"></i> Sudah Siap
                                </button>
                            </form>
                        `;
                        actionsCell.insertAdjacentHTML('beforeend', formHtml);
                    }
                }

                // Add highlight animation
                row.classList.add('row-updated');
                setTimeout(() => {
                    row.classList.remove('row-updated');
                }, 2000);
            }
        }

        // Update timestamp
        const dateCell = row.querySelector('.request-date');
        if (dateCell) {
            dateCell.textContent = this.formatDate(request.updated_at);
        }

        // Update item count if changed
        const itemCountCell = row.querySelector('td:nth-child(4)');
        if (itemCountCell && request.item_count !== undefined) {
            const currentText = itemCountCell.textContent.trim();
            const newText = `${request.item_count} item${request.item_count !== 1 ? 's' : ''}`;
            if (currentText !== newText) {
                itemCountCell.textContent = newText;
            }
        }

        this.log(`Updated request row: ${request.id}`);
    }
    
    /**
     * Add new request row
     */
    addNewRequestRow(tbody, request) {
        const row = this.createRequestRow(request);
        
        // Add with animation
        row.classList.add('row-new');
        
        // FIXED: Insert at the TOP of the table (newest first)
        // Using insertBefore instead of appendChild to put new requests at the top
        if (tbody.firstChild) {
            tbody.insertBefore(row, tbody.firstChild);
        } else {
            tbody.appendChild(row);
        }
        
        // Remove animation class after animation completes
        setTimeout(() => {
            row.classList.remove('row-new');
        }, 3000);
        
        // Show notification if enabled
        if (this.options.enableNotifications) {
            this.showNotification('New Request', `Request ${request.request_number} has been added`);
        }
        
        this.log(`Added new request row at top: ${request.id}`);
    }
    
    /**
     * Remove request row
     */
    removeRequestRow(row) {
        row.classList.add('row-removed');
        
        setTimeout(() => {
            row.remove();
        }, 500);
        
        this.log(`Removed request row: ${row.dataset.requestId}`);
    }
    
    /**
     * Create request row HTML
     */
    createRequestRow(request) {
        const row = document.createElement('tr');
        row.dataset.requestId = request.id;
        row.dataset.createdAt = request.created_at;  // Add timestamp for sorting
        row.dataset.updatedAt = request.updated_at; // Add update timestamp for sorting
        row.className = 'hover:bg-gray-50';
        
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                ${request.request_number}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <div>
                    <div class="font-medium text-gray-900">${request.production_user_name}</div>
                    <div class="text-xs text-gray-500">${request.production_division}</div>
                </div>
            </td>
            <td class="request-date px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                ${this.formatDate(request.created_at)}
            </td>
            <td class="px-6 py-4 text-sm text-gray-500">
                ${(request.item_count ?? 0)} item${(request.item_count ?? 0) !== 1 ? 's' : ''}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="request-status px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${this.getStatusColorClasses(request.status)}">
                    ${this.getStatusLabel(request.status)}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex space-x-2">
                    <button onclick="viewRequest(${request.id})" class="text-blue-600 hover:text-blue-900">
                        <i class="bi bi-eye"></i> Lihat
                    </button>

                    ${request.status === 'pending' ? `
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="update_status">
                            <input type="hidden" name="request_id" value="${request.id}">
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" onclick="return confirm('Setujui permintaan ini?')" class="text-green-600 hover:text-green-900">
                                <i class="bi bi-check-circle"></i> Setujui
                            </button>
                        </form>
                    ` : ''}

                    ${request.status === 'approved' ? `
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="update_status">
                            <input type="hidden" name="request_id" value="${request.id}">
                            <input type="hidden" name="status" value="ready">
                            <button type="submit" onclick="return confirm('Tandai sebagai Sudah Siap?')" class="text-purple-600 hover:text-purple-900">
                                <i class="bi bi-check2-circle"></i> Sudah Siap
                            </button>
                        </form>
                    ` : ''}
                </div>
            </td>
        `;
        
        return row;
    }
    
    /**
     * Update pagination
     */
    updatePagination(pagination) {
        const paginationContainer = document.querySelector(this.options.paginationSelector);
        if (!paginationContainer) {
            return;
        }
        
        // Update pagination info
        const infoElement = paginationContainer.querySelector('.pagination-info');
        if (infoElement) {
            infoElement.textContent = `Showing ${pagination.start_record} to ${pagination.end_record} of ${pagination.total_records} results`;
        }
        
        this.log('Pagination updated');
    }
    
    /**
     * Handle errors
     */
    handleError(error) {
        this.log(`Error fetching updates: ${error.message}`);
        
        // Check for authentication errors specifically
        if (error.message.includes('Authentication failed') || 
            error.message.includes('401') || 
            error.message.includes('403')) {
            this.handleAuthenticationError(error);
            return;
        }
        
        this.retryCount++;
        this.connectionStatus = 'error';
        this.notifyConnectionChange();
        
        if (this.retryCount >= this.options.maxRetries) {
            this.log('Max retries reached, stopping updater');
            this.connectionStatus = 'disconnected';
            this.notifyConnectionChange();
            
            // Show user-friendly message after multiple failures
            this.showConnectionErrorMessage(error);
            return;
        }
        
        // Exponential backoff for retries
        const retryDelay = this.options.retryDelay * Math.pow(2, this.retryCount - 1);
        this.log(`Retrying in ${retryDelay}ms (attempt ${this.retryCount}/${this.options.maxRetries})`);
        
        setTimeout(() => {
            this.fetchUpdates();
        }, retryDelay);
    }
    
    /**
     * Handle authentication errors
     */
    handleAuthenticationError(error) {
        this.log('Authentication error detected, stopping updater');
        this.connectionStatus = 'session_expired';
        this.notifyConnectionChange();
        
        // Show authentication error message
        this.showAuthenticationErrorMessage(error);
        
        // Stop updater - requires page refresh
        this.stop();
        this.retryCount = this.options.maxRetries;
    }
    
    /**
     * Show authentication error message
     */
    showAuthenticationErrorMessage(error) {
        // Remove any existing error modals
        const existingErrorModal = document.getElementById('authErrorModal');
        if (existingErrorModal) {
            existingErrorModal.remove();
        }
        
        const modal = document.createElement('div');
        modal.id = 'authErrorModal';
        modal.style.cssText = `
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            background-color: rgba(0, 0, 0, 0.7) !important;
            z-index: 99999 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        `;
        
        modal.innerHTML = `
            <div style="
                background: white !important;
                border-radius: 12px !important;
                max-width: 450px !important;
                width: 90% !important;
                padding: 32px !important;
                text-align: center !important;
                box-shadow: 0 25px 50px rgba(0,0,0,0.35) !important;
            ">
                <div style="
                    font-size: 64px !important;
                    color: #ef4444 !important;
                    margin-bottom: 20px !important;
                ">🔐</div>
                <h3 style="
                    font-size: 20px !important;
                    font-weight: 600 !important;
                    color: #111827 !important;
                    margin-bottom: 12px !important;
                ">Authentication Error</h3>
                <p style="
                    font-size: 16px !important;
                    color: #6b7280 !important;
                    margin-bottom: 8px !important;
                    line-height: 1.5 !important;
                ">Your session has expired or authentication failed.</p>
                <p style="
                    font-size: 14px !important;
                    color: #9ca3af !important;
                    margin-bottom: 24px !important;
                ">Please refresh the page to log in again.</p>
                <div style="display: flex; gap: 12px; justify-content: center;">
                    <button onclick="window.location.reload()" style="
                        background: #10b981 !important;
                        color: white !important;
                        padding: 12px 24px !important;
                        border: none !important;
                        border-radius: 6px !important;
                        cursor: pointer !important;
                        font-weight: 500 !important;
                        font-size: 14px !important;
                    ">Refresh Page</button>
                    <button onclick="this.closest('#authErrorModal').remove()" style="
                        background: #6b7280 !important;
                        color: white !important;
                        padding: 12px 24px !important;
                        border: none !important;
                        border-radius: 6px !important;
                        cursor: pointer !important;
                        font-weight: 500 !important;
                        font-size: 14px !important;
                    ">Cancel</button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
    }
    
    /**
     * Show connection error message after multiple failures
     */
    showConnectionErrorMessage(error) {
        // Remove any existing error modals
        const existingErrorModal = document.getElementById('connectionErrorModal');
        if (existingErrorModal) {
            existingErrorModal.remove();
        }
        
        // Try to parse additional error details from the last response
        let errorDetails = error.message || 'Unknown error';
        let technicalDetails = '';
        
        // Check if we have a fetch response with JSON data
        if (this.lastErrorData && this.lastErrorData.debug_info) {
            const debug = this.lastErrorData.debug_info;
            technicalDetails = `
                <div style="text-align: left; background: #f3f4f6; padding: 12px; border-radius: 6px; margin: 16px 0; font-size: 12px; font-family: monospace;">
                    <strong>Technical Details:</strong><br>
                    ${Object.entries(debug).map(([key, value]) => `${key}: ${JSON.stringify(value)}`).join('<br>')}
                </div>
            `;
        }
        
        const modal = document.createElement('div');
        modal.id = 'connectionErrorModal';
        modal.style.cssText = `
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            background-color: rgba(0, 0, 0, 0.7) !important;
            z-index: 99999 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        `;
        
        modal.innerHTML = `
            <div style="
                background: white !important;
                border-radius: 12px !important;
                max-width: 500px !important;
                width: 90% !important;
                padding: 32px !important;
                text-align: center !important;
                box-shadow: 0 25px 50px rgba(0,0,0,0.35) !important;
            ">
                <div style="
                    font-size: 64px !important;
                    color: #ef4444 !important;
                    margin-bottom: 20px !important;
                ">📡</div>
                <h3 style="
                    font-size: 20px !important;
                    font-weight: 600 !important;
                    color: #111827 !important;
                    margin-bottom: 12px !important;
                ">Connection Error</h3>
                <p style="
                    font-size: 16px !important;
                    color: #6b7280 !important;
                    margin-bottom: 8px !important;
                    line-height: 1.5 !important;
                ">Unable to connect to the server for real-time updates.</p>
                <p style="
                    font-size: 14px !important;
                    color: #9ca3af !important;
                    margin-bottom: 8px !important;
                ">Error: ${errorDetails}</p>
                ${technicalDetails}
                <p style="
                    font-size: 13px !important;
                    color: #9ca3af !important;
                    margin-bottom: 24px !important;
                ">The dashboard will continue to work, but updates may require manual refresh.</p>
                <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                    <button onclick="window.location.reload()" style="
                        background: #10b981 !important;
                        color: white !important;
                        padding: 12px 24px !important;
                        border: none !important;
                        border-radius: 6px !important;
                        cursor: pointer !important;
                        font-weight: 500 !important;
                        font-size: 14px !important;
                    ">🔄 Refresh Page</button>
                    <button onclick="this.closest('#connectionErrorModal').remove(); window.dashboardUpdater.triggerUpdate();" style="
                        background: #3b82f6 !important;
                        color: white !important;
                        padding: 12px 24px !important;
                        border: none !important;
                        border-radius: 6px !important;
                        cursor: pointer !important;
                        font-weight: 500 !important;
                        font-size: 14px !important;
                    ">🔃 Retry Now</button>
                    <button onclick="this.closest('#connectionErrorModal').remove()" style="
                        background: #6b7280 !important;
                        color: white !important;
                        padding: 12px 24px !important;
                        border: none !important;
                        border-radius: 6px !important;
                        cursor: pointer !important;
                        font-weight: 500 !important;
                        font-size: 14px !important;
                    ">Continue Anyway</button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
    }
    
    /**
     * Handle session expiry
     */
    handleSessionExpired(data) {
        this.log('Session expired, stopping updater');
        this.stop();
        this.connectionStatus = 'session_expired';
        this.notifyConnectionChange();
        
        // Show user-friendly message
        this.showSessionExpiredMessage(data);
        
        // Don't retry - require page refresh
        this.retryCount = this.options.maxRetries;
    }
    
    /**
     * Show session expired message
     */
    showSessionExpiredMessage(data) {
        const message = data.error || 'Your session has expired';
        
        // Create notification modal
        const modal = document.createElement('div');
        modal.style.cssText = `
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            background-color: rgba(0, 0, 0, 0.5) !important;
            z-index: 99999 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        `;
        
        modal.innerHTML = `
            <div style="
                background: white !important;
                border-radius: 12px !important;
                max-width: 400px !important;
                width: 90% !important;
                padding: 24px !important;
                text-align: center !important;
                box-shadow: 0 25px 50px rgba(0,0,0,0.25) !important;
            ">
                <div style="
                    font-size: 48px !important;
                    color: #ef4444 !important;
                    margin-bottom: 16px !important;
                ">⚠️</div>
                <h3 style="
                    font-size: 18px !important;
                    font-weight: 600 !important;
                    color: #111827 !important;
                    margin-bottom: 8px !important;
                ">Session Expired</h3>
                <p style="
                    font-size: 14px !important;
                    color: #6b7280 !important;
                    margin-bottom: 20px !important;
                ">${message}</p>
                <button onclick="window.location.reload()" style="
                    background: #10b981 !important;
                    color: white !important;
                    padding: 10px 20px !important;
                    border: none !important;
                    border-radius: 6px !important;
                    cursor: pointer !important;
                    font-weight: 500 !important;
                ">Refresh Page</button>
            </div>
        `;
        
        document.body.appendChild(modal);
    }
    
    /**
     * Update performance metrics
     */
    updatePerformanceMetrics(responseTime) {
        this.requestCount++;
        
        // Calculate rolling average
        if (this.averageResponseTime === 0) {
            this.averageResponseTime = responseTime;
        } else {
            this.averageResponseTime = (this.averageResponseTime * 0.9) + (responseTime * 0.1);
        }
        
        this.log(`Response time: ${responseTime.toFixed(2)}ms, Average: ${this.averageResponseTime.toFixed(2)}ms`);
    }
    
    /**
     * Notify connection status change
     */
    notifyConnectionChange() {
        if (this.options.onConnectionChange) {
            this.options.onConnectionChange(this.connectionStatus);
        }
        
        this.updateConnectionIndicator();
    }
    
    /**
     * Update connection indicator
     */
    updateConnectionIndicator() {
        let indicator = document.querySelector('.connection-indicator');
        
        if (!indicator) {
            // Create connection indicator if it doesn't exist
            indicator = document.createElement('div');
            indicator.className = 'connection-indicator fixed bottom-4 right-4 px-3 py-1 rounded-full text-xs font-medium z-50';
            document.body.appendChild(indicator);
        }
        
        // Update indicator appearance based on status
        indicator.className = 'connection-indicator fixed bottom-4 right-4 px-3 py-1 rounded-full text-xs font-medium z-50';
        
        switch (this.connectionStatus) {
            case 'connected':
                indicator.classList.add('bg-green-100', 'text-green-800');
                indicator.textContent = 'Connected';
                break;
            case 'connecting':
                indicator.classList.add('bg-yellow-100', 'text-yellow-800');
                indicator.textContent = 'Connecting...';
                break;
            case 'session_expired':
                indicator.classList.add('bg-red-100', 'text-red-800');
                indicator.textContent = 'Session Expired';
                break;
            case 'error':
                indicator.classList.add('bg-red-100', 'text-red-800');
                indicator.textContent = 'Connection Error';
                break;
            default:
                indicator.classList.add('bg-gray-100', 'text-gray-800');
                indicator.textContent = 'Disconnected';
        }
    }
    
    /**
     * Show browser notification
     */
    showNotification(title, message) {
        if (!('Notification' in window)) {
            return;
        }
        
        if (Notification.permission === 'granted') {
            new Notification(title, {
                body: message,
                icon: '/favicon.ico'
            });
        } else if (Notification.permission !== 'denied') {
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    new Notification(title, {
                        body: message,
                        icon: '/favicon.ico'
                    });
                }
            });
        }
    }
    
    /**
     * Get status label in Indonesian
     */
    getStatusLabel(status) {
        const statusLabels = {
            'pending': 'Menunggu',
            'approved': 'Disetujui',
            'ready': 'Sudah Siap',
            'completed': 'Selesai',
            'cancelled': 'Dibatalkan'
        };

        return statusLabels[status] || status;
    }

    /**
     * Get Tailwind color classes for status badge
     */
    getStatusColorClasses(status) {
        const colorClasses = {
            'pending': 'bg-yellow-100 text-yellow-800',
            'approved': 'bg-blue-100 text-blue-800',
            'ready': 'bg-purple-100 text-purple-800',
            'completed': 'bg-green-100 text-green-800',
            'cancelled': 'bg-red-100 text-red-800'
        };

        return colorClasses[status] || 'bg-gray-100 text-gray-800';
    }
    
    /**
     * Format date
     */
    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleString('id-ID', {
            month: 'short',
            day: 'numeric',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
    
    /**
     * Log messages if debug mode is enabled (with rotation to prevent quota issues)
     */
    log(message, data = null) {
        if (this.options.debugMode) {
            // Limit log entries to prevent quota issues
            if (!this.logEntries) {
                this.logEntries = [];
            }
            
            this.logEntries.push({
                timestamp: Date.now(),
                message: message,
                data: data
            });
            
            // Keep only last 50 log entries
            if (this.logEntries.length > 50) {
                this.logEntries = this.logEntries.slice(-50);
            }
            
            // Log to console with data limit
            if (data && typeof data === 'object' && Object.keys(data).length > 10) {
                console.log(`[DashboardUpdater] ${message}`, 'Object too large for console');
            } else {
                console.log(`[DashboardUpdater] ${message}`, data || '');
            }
        }
    }
    
    /**
     * Public method to get current status
     */
    getStatus() {
        return {
            isRunning: this.isRunning,
            isPaused: this.isPaused,
            connectionStatus: this.connectionStatus,
            currentInterval: this.currentInterval,
            retryCount: this.retryCount,
            averageResponseTime: this.averageResponseTime,
            requestCount: this.requestCount
        };
    }
    
    /**
     * Public method to manually trigger update
     */
    triggerUpdate() {
        this.fetchUpdates();
    }
    
    /**
     * Destroy the updater
     */
    destroy() {
        this.stop();
        
        // Clean up resources to prevent memory leaks
        if (this.pollTimer) {
            clearTimeout(this.pollTimer);
            this.pollTimer = null;
        }
        
        // Abort any in-progress request
        if (this.currentRequestController) {
            this.currentRequestController.abort();
            this.currentRequestController = null;
        }
        
        // Clear stored data
        this.visibleRequests.clear();
        this.knownRequests.clear();
        
        // Clear log entries
        if (this.logEntries) {
            this.logEntries = [];
        }
        
        // Clear DOM element caches
        if (this._statsElements) {
            this._statsElements = null;
        }
        
        // Clear animation timers
        if (this._animationTimers) {
            this._animationTimers.forEach(timer => clearTimeout(timer));
            this._animationTimers.clear();
            this._animationTimers = null;
        }
        
        // Remove connection indicator
        const indicator = document.querySelector('.connection-indicator');
        if (indicator) {
            indicator.remove();
        }
        
        // Remove error modals
        const errorModals = ['authErrorModal', 'connectionErrorModal', 'sessionExpiredModal'];
        errorModals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.remove();
            }
        });
        
        // Clean up viewRequest modal cache
        if (window.viewRequest && window.viewRequest.modalCache) {
            window.viewRequest.modalCache = null;
        }
        
        // Clean up event listeners by removing the global reference
        if (window.dashboardUpdater === this) {
            window.dashboardUpdater = null;
        }
        
        this.log('Dashboard updater destroyed and cleaned up');
    }
}

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Check if dashboard container exists
    const dashboardContainer = document.querySelector('.dashboard-container');
    
    if (dashboardContainer) {
        // Initialize dashboard updater with default options
        window.dashboardUpdater = new DashboardUpdater({
            debugMode: true, // Enable debug mode for development
            enableNotifications: true,
            onConnectionChange: function(status) {
                console.log('Connection status changed:', status);
            },
            onStatsUpdate: function(stats) {
                console.log('Statistics updated:', stats);
            },
            onRequestUpdate: function(requests) {
                console.log('Requests updated:', requests.length);
            }
        });
        
        console.log('Dashboard updater initialized');
    }
});

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = DashboardUpdater;
}
