<!-- ===== MODAL BÁO CÁO VI PHẠM ===== -->
<div id="global-report-modal" class="hidden fixed inset-0 z-[100] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="glass-panel rounded-2xl w-full max-w-md shadow-2xl scale-95 transition-transform duration-300">
        <div class="flex items-center justify-between p-4 border-b border-sky-400/10">
            <h3 class="text-lg font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-500">report</span>
                Báo cáo vi phạm
            </h3>
            <button type="button" id="close-report-modal" class="p-2 text-slate-400 hover:text-white hover:bg-white/10 rounded-full transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form id="global-report-form" class="p-4 flex flex-col gap-4">
            @csrf
            <!-- Hidden target fields -->
            <input type="hidden" name="bai_viet_id" id="report-post-id" value="">
            <input type="hidden" name="binh_luan_id" id="report-comment-id" value="">
            <input type="hidden" name="nguoi_dung_bi_bao_cao_id" id="report-user-id" value="">

            <div class="space-y-3">
                <label class="text-sm font-semibold text-slate-300">Lý do báo cáo:</label>
                
                <div class="space-y-2">
                    <label class="flex items-center gap-3 p-3 rounded-xl border border-white/5 bg-slate-900/30 hover:bg-white/5 cursor-pointer transition-all">
                        <input type="radio" name="predefined_reason" value="Nội dung phản cảm, thù ghét" class="text-sky-500 focus:ring-sky-500 border-white/10 bg-slate-950" checked>
                        <span class="text-sm text-slate-250">Nội dung phản cảm, thù ghét</span>
                    </label>

                    <label class="flex items-center gap-3 p-3 rounded-xl border border-white/5 bg-slate-900/30 hover:bg-white/5 cursor-pointer transition-all">
                        <input type="radio" name="predefined_reason" value="Bạo lực, quấy rối" class="text-sky-500 focus:ring-sky-500 border-white/10 bg-slate-950">
                        <span class="text-sm text-slate-250">Bạo lực, quấy rối</span>
                    </label>

                    <label class="flex items-center gap-3 p-3 rounded-xl border border-white/5 bg-slate-900/30 hover:bg-white/5 cursor-pointer transition-all">
                        <input type="radio" name="predefined_reason" value="Spam hoặc tin giả" class="text-sky-500 focus:ring-sky-500 border-white/10 bg-slate-950">
                        <span class="text-sm text-slate-250">Spam hoặc tin giả</span>
                    </label>

                    <label class="flex items-center gap-3 p-3 rounded-xl border border-white/5 bg-slate-900/30 hover:bg-white/5 cursor-pointer transition-all">
                        <input type="radio" name="predefined_reason" value="Vi phạm bản quyền" class="text-sky-500 focus:ring-sky-500 border-white/10 bg-slate-950">
                        <span class="text-sm text-slate-250">Vi phạm bản quyền</span>
                    </label>

                    <label class="flex items-center gap-3 p-3 rounded-xl border border-white/5 bg-slate-900/30 hover:bg-white/5 cursor-pointer transition-all">
                        <input type="radio" name="predefined_reason" value="Khác" id="reason-other-radio" class="text-sky-500 focus:ring-sky-500 border-white/10 bg-slate-950">
                        <span class="text-sm text-slate-250">Lý do khác...</span>
                    </label>
                </div>
            </div>

            <!-- Custom reason description (hidden by default unless "Khác" is checked) -->
            <div id="custom-reason-container" class="hidden">
                <textarea name="custom_reason" id="report-custom-reason" rows="3" placeholder="Nhập lý do chi tiết..." class="w-full bg-slate-900/50 border border-sky-400/20 rounded-xl focus:ring-1 focus:ring-sky-400 text-slate-100 placeholder-slate-500 resize-none p-3 text-sm"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" id="cancel-report-btn" class="px-5 py-2 text-sm font-semibold text-slate-300 hover:text-white hover:bg-white/5 rounded-xl transition-colors">Hủy</button>
                <button type="submit" id="submit-report-btn" class="px-5 py-2 text-sm font-semibold bg-amber-500 hover:bg-amber-450 text-slate-950 rounded-xl shadow-lg shadow-amber-500/20 transition-all active:scale-95 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">send</span> Gửi báo cáo
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('global-report-modal');
    const form = document.getElementById('global-report-form');
    const closeBtn = document.getElementById('close-report-modal');
    const cancelBtn = document.getElementById('cancel-report-btn');
    const submitBtn = document.getElementById('submit-report-btn');
    
    const radioButtons = document.querySelectorAll('input[name="predefined_reason"]');
    const customContainer = document.getElementById('custom-reason-container');
    const customTextarea = document.getElementById('report-custom-reason');
    
    // Listen to radio updates
    radioButtons.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'Khác') {
                customContainer.classList.remove('hidden');
                customTextarea.required = true;
                customTextarea.focus();
            } else {
                customContainer.classList.add('hidden');
                customTextarea.required = false;
                customTextarea.value = '';
            }
        });
    });

    // Close Modal Functions
    window.closeReportModal = function() {
        modal.classList.add('opacity-0');
        const modalContent = modal.querySelector('.glass-panel');
        if (modalContent) modalContent.classList.add('scale-95');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            form.reset();
            customContainer.classList.add('hidden');
            customTextarea.required = false;
        }, 300);
    };

    // Open Modal Function
    window.openReportModal = function(targets) {
        // targets = { bai_viet_id: 123, binh_luan_id: 456, nguoi_dung_bi_bao_cao_id: 789 }
        document.getElementById('report-post-id').value = targets.bai_viet_id || '';
        document.getElementById('report-comment-id').value = targets.binh_luan_id || '';
        document.getElementById('report-user-id').value = targets.nguoi_dung_bi_bao_cao_id || '';
        
        // Hide all dropdown menus globally
        document.querySelectorAll('.post-dropdown-menu').forEach(m => m.classList.add('hidden'));

        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            modal.classList.remove('opacity-0');
            const modalContent = modal.querySelector('.glass-panel');
            if (modalContent) modalContent.classList.remove('scale-95');
        });
    };

    if (closeBtn) closeBtn.addEventListener('click', window.closeReportModal);
    if (cancelBtn) cancelBtn.addEventListener('click', window.closeReportModal);
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) window.closeReportModal();
        });
    }

    // Submit report via AJAX
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const selectedRadio = document.querySelector('input[name="predefined_reason"]:checked');
        if (!selectedRadio) return;
        
        let reason = selectedRadio.value;
        if (reason === 'Khác') {
            reason = customTextarea.value.trim();
            if (!reason) {
                if(typeof window.showToast === 'function') window.showToast('Vui lòng điền lý do chi tiết.', 'error');
                return;
            }
        }
        
        const postId = document.getElementById('report-post-id').value;
        const commentId = document.getElementById('report-comment-id').value;
        const userId = document.getElementById('report-user-id').value;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="material-symbols-outlined text-[18px] animate-spin">refresh</span> Đang gửi...';
        
        fetch('{{ route("reports.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                ly_do: reason,
                bai_viet_id: postId || null,
                binh_luan_id: commentId || null,
                nguoi_dung_bi_bao_cao_id: userId || null
            })
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(({ status, body }) => {
            if (status === 200 && body.success) {
                if(typeof window.showToast === 'function') {
                    window.showToast(body.message, 'success');
                } else {
                    alert(body.message);
                }
                window.closeReportModal();
            } else {
                const errMsg = body.message || 'Có lỗi xảy ra khi gửi báo cáo.';
                if(typeof window.showToast === 'function') {
                    window.showToast(errMsg, 'error');
                } else {
                    alert(errMsg);
                }
            }
        })
        .catch(err => {
            console.error('Error submitting report:', err);
            if(typeof window.showToast === 'function') {
                window.showToast('Lỗi kết nối đến máy chủ.', 'error');
            } else {
                alert('Lỗi kết nối đến máy chủ.');
            }
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<span class="material-symbols-outlined text-[18px]">send</span> Gửi báo cáo';
        });
    });
});
</script>
