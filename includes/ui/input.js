// Input component for shadcn/ui
function createInput(type = 'text', placeholder = '', className = '', name = '', required = false, value = '') {
  const baseClasses = 'flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50';
  
  const attributes = [];
  if (name) attributes.push(`name="${name}"`);
  if (placeholder) attributes.push(`placeholder="${placeholder}"`);
  if (required) attributes.push('required');
  if (value) attributes.push(`value="${value}"`);
  
  return `<input type="${type}" class="${baseClasses} ${className}" ${attributes.join(' ')} />`;
}

// Export for use in other files
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { createInput };
}
