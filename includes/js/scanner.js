/**
 * Simple Scanner Input Handler
 * Handles rapid barcode/QR code scanning with input buffering and timeout detection
 */

class ScannerInputHandler {
    constructor(options = {}) {
        // Configuration options
        this.options = {
            inputSelector: options.inputSelector || '#lot_material_bc',
            timeoutDuration: options.timeoutDuration || 1000, // 1 second default
            minScanLength: options.minScanLength || 3,
            debugMode: options.debugMode || false,
            onScanComplete: options.onScanComplete || this.defaultScanHandler,
            onScanBufferUpdate: options.onScanBufferUpdate || null
        };
        
        // Internal state
        this.inputElement = null;
        this.scanBuffer = '';
        this.scanTimeout = null;
        this.isProcessing = false;
        this.lastScanTime = 0;
        
        // Initialize the scanner
        this.init();
    }
    
    /**
     * Initialize the scanner handler
     */
    init() {
        // Find the input element
        this.inputElement = document.querySelector(this.options.inputSelector);
        
        if (!this.inputElement) {
            console.error(`ScannerInputHandler: Input element not found: ${this.options.inputSelector}`);
            return;
        }
        
        // Set up event listeners
        this.setupEventListeners();
        
        this.log('Scanner input handler initialized');
    }
    
    /**
     * Set up event listeners for the input element
     */
    setupEventListeners() {
        // Listen for input events
        this.inputElement.addEventListener('input', (e) => this.handleInput(e));
        
        // Listen for keydown events to detect special keys
        this.inputElement.addEventListener('keydown', (e) => this.handleKeyDown(e));
        
        // Listen for paste events (for manual testing)
        this.inputElement.addEventListener('paste', (e) => this.handlePaste(e));
    }
    
    /**
     * Handle input events from the scanner
     */
    handleInput(event) {
        if (this.isProcessing) return;
        
        const currentValue = event.target.value;
        
        // If the input is empty, reset the buffer
        if (!currentValue) {
            this.resetBuffer();
            return;
        }
        
        // Add to buffer
        this.addToBuffer(currentValue);
    }
    
    /**
     * Handle keydown events to detect special scanner keys
     */
    handleKeyDown(event) {
        // Some scanners send a specific key combination to indicate scan completion
        // This is scanner-dependent and may need customization
        
        // Tab or Enter often indicates scan completion
        if (event.key === 'Tab' || event.key === 'Enter') {
            event.preventDefault();
            this.processScan();
        }
    }
    
    /**
     * Handle paste events (for manual testing)
     */
    handlePaste(event) {
        // Allow paste for testing but process it immediately
        setTimeout(() => {
            this.processScan();
        }, 100);
    }
    
    /**
     * Add input to the scan buffer
     */
    addToBuffer(value) {
        this.scanBuffer = value;
        this.lastScanTime = Date.now();
        
        // Reset the timeout
        this.resetTimeout();
        
        // Notify callback if provided
        if (this.options.onScanBufferUpdate) {
            this.options.onScanBufferUpdate(this.scanBuffer);
        }
        
        this.log(`Buffer updated: ${this.scanBuffer}`);
    }
    
    /**
     * Reset the scan timeout
     */
    resetTimeout() {
        // Clear existing timeout
        if (this.scanTimeout) {
            clearTimeout(this.scanTimeout);
        }
        
        // Set new timeout
        this.scanTimeout = setTimeout(() => {
            this.processScan();
        }, this.options.timeoutDuration);
    }
    
    /**
     * Process the current scan
     */
    processScan() {
        if (this.isProcessing) return;
        
        // Clear timeout
        if (this.scanTimeout) {
            clearTimeout(this.scanTimeout);
            this.scanTimeout = null;
        }
        
        // Get the scan data
        const scanData = this.scanBuffer.trim();
        
        // Validate scan data
        if (!this.validateScan(scanData)) {
            this.resetBuffer();
            return;
        }
        
        // Set processing flag
        this.isProcessing = true;
        
        this.log(`Processing scan: ${scanData}`);
        
        // Call the scan handler
        try {
            this.options.onScanComplete(scanData, this);
        } catch (error) {
            console.error('Error in scan handler:', error);
        }
        
        // Reset after processing
        setTimeout(() => {
            this.resetBuffer();
            this.isProcessing = false;
        }, 500);
    }
    
    /**
     * Validate the scan data
     */
    validateScan(scanData) {
        // Check minimum length
        if (scanData.length < this.options.minScanLength) {
            this.log(`Scan too short: ${scanData}`);
            return false;
        }
        
        // Additional validation can be added here
        // For example, check for specific patterns
        
        return true;
    }
    
    /**
     * Reset the scan buffer
     */
    resetBuffer() {
        this.scanBuffer = '';
        
        // Clear timeout
        if (this.scanTimeout) {
            clearTimeout(this.scanTimeout);
            this.scanTimeout = null;
        }
        
        // Clear input field
        if (this.inputElement) {
            this.inputElement.value = '';
        }
        
        this.log('Buffer reset');
    }
    
    /**
     * Default scan handler
     */
    defaultScanHandler(scanData) {
        this.log(`Default handler processed: ${scanData}`);
        
        // Submit the form if it exists
        const form = this.inputElement.form;
        if (form) {
            form.submit();
        }
    }
    
    /**
     * Log messages if debug mode is enabled
     */
    log(message) {
        if (this.options.debugMode) {
            console.log(`[Scanner] ${message}`);
        }
    }
    
    /**
     * Public method to manually trigger a scan
     */
    triggerScan(scanData) {
        this.scanBuffer = scanData;
        this.processScan();
    }
    
    /**
     * Public method to get current buffer content
     */
    getBuffer() {
        return this.scanBuffer;
    }
    
    /**
     * Public method to check if currently processing
     */
    isScanning() {
        return this.isProcessing;
    }
    
    /**
     * Destroy the scanner handler
     */
    destroy() {
        // Clear timeout
        if (this.scanTimeout) {
            clearTimeout(this.scanTimeout);
        }
        
        // Remove event listeners
        if (this.inputElement) {
            this.inputElement.removeEventListener('input', this.handleInput);
            this.inputElement.removeEventListener('keydown', this.handleKeyDown);
            this.inputElement.removeEventListener('paste', this.handlePaste);
        }
        
        this.log('Scanner handler destroyed');
    }
}

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Check if scanner input exists on the page
    const scannerInput = document.querySelector('#lot_material_bc');
    
    if (scannerInput) {
        // Initialize scanner with default options
        window.scannerHandler = new ScannerInputHandler({
            debugMode: true, // Enable debug mode for development
            onScanComplete: function(scanData, handler) {
                console.log('Scan completed:', scanData);
                
                // Show loading indicator
                const loadingOverlay = document.getElementById('loadingOverlay');
                if (loadingOverlay) {
                    loadingOverlay.classList.remove('hidden');
                }
                
                // Submit the form
                const form = scannerInput.form;
                if (form) {
                    form.submit();
                }
            }
        });
        
        console.log('Scanner input handler initialized');
    }
});

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ScannerInputHandler;
}
