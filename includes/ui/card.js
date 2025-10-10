// Card component for shadcn/ui
function createCard(className = '', children = '') {
  return `<div class="rounded-lg border bg-card text-card-foreground shadow-sm ${className}">${children}</div>`;
}

function createCardHeader(className = '', children = '') {
  return `<div class="flex flex-col space-y-1.5 p-6 ${className}">${children}</div>`;
}

function createCardTitle(className = '', children = '') {
  return `<h3 class="text-2xl font-semibold leading-none tracking-tight ${className}">${children}</h3>`;
}

function createCardDescription(className = '', children = '') {
  return `<p class="text-sm text-muted-foreground ${className}">${children}</p>`;
}

function createCardContent(className = '', children = '') {
  return `<div class="p-6 pt-0 ${className}">${children}</div>`;
}

function createCardFooter(className = '', children = '') {
  return `<div class="flex items-center p-6 pt-0 ${className}">${children}</div>`;
}

// Export for use in other files
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { 
    createCard, 
    createCardHeader, 
    createCardTitle, 
    createCardDescription, 
    createCardContent, 
    createCardFooter 
  };
}
