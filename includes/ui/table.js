// Table component for shadcn/ui
function createTable(className = '', children = '') {
  return `<div class="relative w-full overflow-auto"><table class="w-full caption-bottom text-sm ${className}">${children}</table></div>`;
}

function createTableHeader(className = '', children = '') {
  return `<thead class="${className}">${children}</thead>`;
}

function createTableBody(className = '', children = '') {
  return `<tbody class="${className}">${children}</tbody>`;
}

function createTableFooter(className = '', children = '') {
  return `<tfoot class="border-t bg-muted/50 font-medium ${className}">${children}</tfoot>`;
}

function createTableRow(className = '', children = '') {
  return `<tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted ${className}">${children}</tr>`;
}

function createTableHead(className = '', children = '') {
  return `<th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground ${className}">${children}</th>`;
}

function createTableCell(className = '', children = '') {
  return `<td class="p-4 align-middle ${className}">${children}</td>`;
}

function createTableCaption(className = '', children = '') {
  return `<caption class="mt-4 text-sm text-muted-foreground ${className}">${children}</caption>`;
}

// Export for use in other files
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { 
    createTable, 
    createTableHeader, 
    createTableBody, 
    createTableFooter, 
    createTableRow, 
    createTableHead, 
    createTableCell, 
    createTableCaption 
  };
}
