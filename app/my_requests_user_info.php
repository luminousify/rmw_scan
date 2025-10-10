            <div>
                  <label class="text-sm font-medium text-gray-500 block mb-2">Created By</label>
                  <p class="text-lg text-gray-900">
                    <i class="bi bi-person-circle mr-1"></i>
                    ${request.created_by ?? 'System'}
                  </p>
                  <p class="text-sm text-gray-500">
                    <time datetime="${request.created_at}">${new Date(request.created_at).toLocaleDateString('en-US', { 
                      year: 'numeric', 
                      month: 'long', 
                      day: 'numeric',
                      hour: '2-digit',
                      minute: '2-digit'
                    })}</time>
                  </p>
                </div>
                ${request.status === 'completed' ? `
                <div>
                  <label class="text-sm font-medium text-gray-500 block mb-2">Completed By</label>
                  <p class="text-lg text-gray-900">
                    <i class="bi bi-check-circle mr-1"></i>
                    ${request.completed_by ?? 'System'}
                  </p>
                  <p class="text-sm text-gray-500">
                    <time datetime="${request.completed_date}">${new Date(request.completed_date).toLocaleDateString('en-US', { 
                      year: 'numeric', 
                      month: 'long', 
                      day: 'numeric',
                      hour: '2-digit',
                      minute: '2-digit'
                    })}</time>
                  </p>
                </div>
              ` : ''}
                ${request.status === 'diproses' ? `
                <div>
                  <label class="text-sm font-medium text-gray-500 block mb-2">Processed By</label>
                  <p class="text-lg text-gray-900">
                    <i class="bi bi-person-check mr-1"></i>
                    ${request.processed_by ?? 'System'}
                  </p>
                  <p class="text-sm text-gray-500">
                    <time datetime="${request.processed_date}">${new Date(request.processed_date).toLocaleDateString('en-US', { 
                      year: 'numeric', 
                      month: 'long', 
                      day: 'numeric',
                      hour: '2-digit',
                      minute: '2-digit'
                    })}</time>
                  </p>
                </div>
              ` : ''}
