// Utility functions for shadcn/ui components
function cn(...inputs) {
  return inputs.filter(Boolean).join(' ');
}

function formatDate(date) {
  return new Intl.DateTimeFormat('en-US', {
    month: 'long',
    day: 'numeric',
    year: 'numeric',
  }).format(date);
}

function formatPrice(price) {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
  }).format(price);
}

// Export for use in other files
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { cn, formatDate, formatPrice };
}
