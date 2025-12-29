<!-- Activity Detail Modal -->
<div id="activityModal" 
     class="hidden fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50"
     onclick="closeActivityModal(event)">
    
    <div class="relative top-10 mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-lg bg-white mb-10"
         onclick="event.stopPropagation()">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-4 border-b">
            <h3 class="text-xl font-semibold text-gray-900" id="modalTitle">
                {{ __('activities.activity_detail') }}
            </h3>
            <button type="button" 
                    onclick="closeActivityModal()"
                    class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <!-- Modal Body -->
        <div class="mt-4" id="modalBody">
            <!-- Content will be loaded dynamically via JavaScript -->
            <div class="flex items-center justify-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
            </div>
        </div>
        
        <!-- Modal Footer -->
        <div class="flex justify-end gap-3 mt-6 pt-4 border-t" id="modalFooter">
            <button type="button" 
                    onclick="closeActivityModal()"
                    class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                {{ __('activities.close') }}
            </button>
        </div>
    </div>
</div>

<script>
// Translation strings for JavaScript
const modalTranslations = {
    activity_detail: @json(__('activities.activity_detail')),
    activity_type: @json(__('activities.activity_type')),
    start_date: @json(__('activities.start_date')),
    end_date: @json(__('activities.end_date')),
    person_in_charge: @json(__('activities.person_in_charge')),
    project_info: @json(__('activities.project_info')),
    name: @json(__('activities.name')),
    owner: @json(__('activities.owner')),
    pic: @json(__('activities.pic')),
    description: @json(__('activities.description')),
    attachment: @json(__('activities.attachment')),
    edit: @json(__('activities.edit')),
    close: @json(__('activities.close')),
    load_failed: @json(__('activities.load_failed')),
    try_again: @json(__('activities.try_again')),
    in_progress: @json(__('activities.in_progress')),
    pending_status: @json(__('activities.pending_status')),
    completed: @json(__('activities.completed')),
    download_file: 'Download File',
    open_link: 'Open Link'
};

// Open modal and load activity details
function openActivityModal(activityIdentifier) {
    const modal = document.getElementById('activityModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalBody = document.getElementById('modalBody');
    const modalFooter = document.getElementById('modalFooter');
    
    // Show modal
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Reset title
    modalTitle.textContent = modalTranslations.activity_detail;
    
    // Show loading spinner
    modalBody.innerHTML = `
        <div class="flex items-center justify-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        </div>
    `;
    
    // Fetch activity details via JSON API
    fetch(`/activities/${activityIdentifier}/json`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to load activity details');
            }
            return response.json();
        })
        .then(data => {
            // Update modal title
            modalTitle.textContent = data.nama_aktivitas;
            
            // Status color mapping
            const statusColors = {
                'Progress': 'bg-blue-100 text-blue-800',
                'Pending': 'bg-yellow-100 text-yellow-800',
                'Done': 'bg-green-100 text-green-800'
            };
            
            // Status label mapping
            const statusLabels = {
                'Progress': modalTranslations.in_progress,
                'Pending': modalTranslations.pending_status,
                'Done': modalTranslations.completed
            };
            
            // Build HTML content
            let html = `
                <div class="space-y-4">
                    <!-- Status Badge -->
                    <div>
                        <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full ${statusColors[data.status]}">
                            ${statusLabels[data.status]}
                        </span>
                    </div>
                    
                    <!-- Info Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-1">${modalTranslations.activity_type}</p>
                            <p class="text-sm text-gray-900">${data.jenis_kegiatan}</p>
                        </div>
                        
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-1">${modalTranslations.start_date}</p>
                            <p class="text-sm text-gray-900">${data.tanggal_mulai}</p>
                        </div>
                        
                        ${data.tanggal_selesai ? `
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-1">${modalTranslations.end_date}</p>
                            <p class="text-sm text-gray-900">${data.tanggal_selesai}</p>
                        </div>
                        ` : ''}
                        
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-1">${modalTranslations.person_in_charge}</p>
                            <p class="text-sm text-gray-900">${data.user.name}</p>
                            <p class="text-xs text-gray-500">${data.user.bagian} - ${data.user.email}</p>
                        </div>
                    </div>
                    
                    <!-- Project Info -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-xs font-medium text-gray-500 mb-2">${modalTranslations.project_info}</p>
                        <div class="space-y-1">
                            <p class="text-sm text-gray-900"><strong>${modalTranslations.name}:</strong> ${data.project.nama}</p>
                            <p class="text-sm text-gray-700"><strong>${modalTranslations.owner}:</strong> ${data.project.pemilik}</p>
                            <p class="text-sm text-gray-700"><strong>${modalTranslations.pic}:</strong> ${data.project.pic}</p>
                        </div>
                    </div>
                    
                    <!-- Description -->
                    ${data.deskripsi ? `
                    <div>
                        <p class="text-xs font-medium text-gray-500 mb-1">${modalTranslations.description}</p>
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">${data.deskripsi}</p>
                    </div>
                    ` : ''}
                    
                    <!-- ✅ FIXED: Lampiran (Support both File & Link) -->
                    ${data.lampiran || data.lampiran_link ? `
                    <div>
                        <p class="text-xs font-medium text-gray-500 mb-3">${modalTranslations.attachment}</p>
                        
                        ${data.lampiran ? `
                            <!-- File Upload - Preview & Download -->
                            <div class="space-y-2 mb-3">
                                <!-- Preview Button -->
                                <a href="${data.lampiran_url}" 
                                target="_blank"
                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition text-sm font-medium w-full justify-center border border-blue-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <span>Lihat File</span>
                                </a>
                                
                                <!-- Download Button -->
                                <a href="/activities/${data.uuid}/download-attachment" 
                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg transition text-sm font-semibold w-full justify-center shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span>Download File</span>
                                </a>
                                
                                <!-- Filename info -->
                                <div class="flex items-center gap-2 px-3 py-2 bg-gray-50 rounded-lg border border-gray-200">
                                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                    </svg>
                                    <span class="text-xs text-gray-600 truncate">${data.lampiran}</span>
                                </div>
                            </div>
                        ` : ''}
                        
                        ${data.lampiran_link ? `
                            <!-- External Link -->
                            <a href="${data.lampiran_link}" 
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-purple-50 hover:bg-purple-100 text-purple-700 rounded-lg transition text-sm font-medium w-full justify-center border border-purple-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                </svg>
                                <span class="truncate max-w-[250px]">${data.lampiran_link}</span>
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>
                        ` : ''}
                    </div>
                    ` : ''}
                </div>
            `;
            
            modalBody.innerHTML = html;
            
            // Update footer with edit button if can_edit
            if (data.can_edit) {
                modalFooter.innerHTML = `
                    <a href="/activities/${data.id}/edit" 
                       class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition">
                        ${modalTranslations.edit}
                    </a>
                    <button type="button" 
                            onclick="closeActivityModal()"
                            class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                        ${modalTranslations.close}
                    </button>
                `;
            } else {
                modalFooter.innerHTML = `
                    <button type="button" 
                            onclick="closeActivityModal()"
                            class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                        ${modalTranslations.close}
                    </button>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            modalBody.innerHTML = `
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-red-600 font-medium">${modalTranslations.load_failed}</p>
                    <p class="text-sm text-gray-500 mt-1">${modalTranslations.try_again}</p>
                </div>
            `;
        });
}

// Close modal
function closeActivityModal(event) {
    const modal = document.getElementById('activityModal');
    
    // Close if clicked outside modal or close button
    if (!event || event.target === modal || event.type === 'click') {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

// Close modal on ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modal = document.getElementById('activityModal');
        if (!modal.classList.contains('hidden')) {
            closeActivityModal();
        }
    }
});
</script>