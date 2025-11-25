/**
 * Scanner Input Handler
 * Handles rapid barcode/QR code scanning with input buffering and timeout detection
 */

class ScannerHandler {
    constructor(options = {}) {
        // Configuration options
        this.options = {
            bufferTimeout: options.bufferTimeout || 500, // ms to wait before considering scan complete
            minScanLength: options.minScanLength || 3,   // minimum characters for valid scan
            maxScanLength: options.maxScanLength || 50,  // maximum characters for valid scan
            onScanComplete: options.onScanComplete || null, // callback when scan is complete
            onScanError: options.onScanError || null     // callback for scan errors
        };
        
        // Internal state
        this.inputBuffer = '';
        this.bufferTimer = null;
        this.isProcessing = false;
        
        // Initialize event listeners
        this.init();
    }
    
    /**
     * Initialize the scanner handler by setting up keyboard event listeners
     */
    init() {
        document.addEventListener('keydown', this.handleKeyDown.bind(this));
        console.log('Scanner handler initialized');
    }
    
    /**
     * Handle keyboard events for scanner input
     * @param {KeyboardEvent} event - The keyboard event
     */
    handleKeyDown(event) {
        // Skip if we're already processing a scan
        if (this.isProcessing) {
            return;
        }
        
        // Most scanners send characters as keydown events followed by Enter
        // We'll capture all characters and process when Enter is detected or timeout occurs
        
        // Ignore modifier keys
        if (event.ctrlKey || event.altKey || event.metaKey) {
            return;
        }
        
        // Handle Enter key (common scanner termination)
        if (event.key === 'Enter') {
            event.preventDefault();
            this.processScan();
            return;
        }
        
        // Handle Escape key to clear buffer
        if (event.key === 'Escape') {
            this.clearBuffer();
            return;
        }
        
        // Handle Tab key (some scanners use Tab instead of Enter)
        if (event.key === 'Tab') {
            event.preventDefault();
            this.processScan();
            return;
        }
        
        // Regular character input
        if (event.key && event.key.length === 1) {
            event.preventDefault();
            this.addToBuffer(event.key);
        }
    }
    
    /**
     * Add a character to the input buffer and reset the timeout
     * @param {string} char - The character to add to the buffer
     */
    addToBuffer(char) {
        this.inputBuffer += char;
        
        // Reset the timer to detect when scanning is complete
        this.resetBufferTimer();
        
        // If buffer exceeds maximum length, process immediately
        if (this.inputBuffer.length >= this.options.maxScanLength) {
            this.processScan();
        }
    }
    
    /**
     * Reset the buffer timeout timer
     */
    resetBufferTimer() {
        // Clear existing timer
        if (this.bufferTimer) {
            clearTimeout(this.bufferTimer);
        }
        
        // Set new timer
        this.bufferTimer = setTimeout(() => {
            this.processScan();
        }, this.options.bufferTimeout);
    }
    
    /**
     * Process the current buffer as a complete scan
     */
    processScan() {
        // Clear the timer
        if (this.bufferTimer) {
            clearTimeout(this.bufferTimer);
            this.bufferTimer = null;
        }
        
        // Get the scan data
        const scanData = this.inputBuffer.trim();
        
        // Clear the buffer
        this.clearBuffer();
        
        // Validate scan data
        if (!this.validateScan(scanData)) {
            return;
        }
        
        // Set processing flag
        this.isProcessing = true;
        
        try {
            // Process the scan data
            this.processScanData(scanData);
        } catch (error) {
            console.error('Error processing scan:', error);
            if (this.options.onScanError) {
                this.options.onScanError(error, scanData);
            }
        } finally {
            // Reset processing flag
            this.isProcessing = false;
        }
    }
    
    /**
     * Validate the scan data
     * @param {string} scanData - The scan data to validate
     * @returns {boolean} - True if valid, false otherwise
     */
    validateScan(scanData) {
        // Check minimum length
        if (scanData.length < this.options.minScanLength) {
            console.warn('Scan too short:', scanData);
            if (this.options.onScanError) {
                this.options.onScanError(new Error('Scan too short'), scanData);
            }
            return false;
        }
        
        // Check maximum length
        if (scanData.length > this.options.maxScanLength) {
            console.warn('Scan too long:', scanData);
            if (this.options.onScanError) {
                this.options.onScanError(new Error('Scan too long'), scanData);
            }
            return false;
        }
        
        return true;
    }
    
    /**
     * Process the validated scan data
     * @param {string} scanData - The validated scan data
     */
    processScanData(scanData) {
        console.log('Processing scan:', scanData);
        
        // Call the callback if provided
        if (this.options.onScanComplete) {
            this.options.onScanComplete(scanData);
        }
        
        // Default processing can be added here
        // For now, we just log the scan data
    }
    
    /**
     * Clear the input buffer
     */
    clearBuffer() {
        this.inputBuffer = '';
    }
    
    /**
     * Destroy the scanner handler and clean up event listeners
     */
    destroy() {
        document.removeEventListener('keydown', this.handleKeyDown.bind(this));
        
        if (this.bufferTimer) {
            clearTimeout(this.bufferTimer);
            this.bufferTimer = null;
        }
        
        this.clearBuffer();
        console.log('Scanner handler destroyed');
    }
}

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ScannerHandler;
}

// Example usage:
/*
const scanner = new ScannerHandler({
    bufferTimeout: 500,
    minScanLength: 3,
    maxScanLength: 50,
    onScanComplete: (scanData) => {
        console.log('Scan complete:', scanData);
        // Handle the scan data, e.g., submit to server
    },
    onScanError: (error, scanData) => {
        console.error('Scan error:', error, scanData);
        // Handle scan errors
    }
});
*/
