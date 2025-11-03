/**
 * Category Management JavaScript
 *
 * Handles AJAX operations, image preview, and slug auto-generation
 * for the Product Category Management feature.
 *
 * @package Putra Elektronik
 * @requires jQuery
 * @requires SweetAlert2
 */

$(document).ready(function() {
    'use strict';

    // Base URL from CI (set in view)
    const BASE_URL = window.BASE_URL || '';
    const CSRF_TOKEN_NAME = window.CSRF_TOKEN_NAME || 'csrf_token';
    const CSRF_HASH = window.CSRF_HASH || '';

    /**
     * Auto-generate slug from category name
     */
    $('#category_name').on('keyup', function() {
        const name = $(this).val();
        if (name) {
            // Generate slug client-side (same logic as helper)
            let slug = name.toLowerCase();
            slug = slug.replace(/\s+/g, '-');
            slug = slug.replace(/[^a-z0-9\-]/g, '');
            slug = slug.replace(/-+/g, '-');
            slug = slug.replace(/^-+|-+$/g, '');

            $('#category_slug').val(slug);
        }
    });

    /**
     * Image preview on file upload
     */
    $('#category_icon').on('change', function() {
        const file = this.files[0];

        if (file) {
            // Validate file size (2MB max)
            if (file.size > 2048000) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ukuran gambar maksimal 2MB'
                });
                $(this).val('');
                $('#image_preview').hide();
                return;
            }

            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Format file harus JPG atau PNG'
                });
                $(this).val('');
                $('#image_preview').hide();
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#image_preview').attr('src', e.target.result).show();
            };
            reader.readAsDataURL(file);
        } else {
            $('#image_preview').hide();
        }
    });

    /**
     * Status toggle (AJAX)
     */
    $('.status-toggle').on('change', function() {
        const $toggle = $(this);
        const categoryId = $toggle.data('category-id');
        const newStatus = $toggle.is(':checked') ? 1 : 0;
        const oldStatus = newStatus === 1 ? 0 : 1;

        // Disable toggle during request
        $toggle.prop('disabled', true);

        $.ajax({
            url: BASE_URL + 'api/category_api/toggle_status',
            method: 'POST',
            dataType: 'json',
            data: {
                category_id: categoryId,
                status: newStatus,
                [CSRF_TOKEN_NAME]: CSRF_HASH
            },
            success: function(response) {
                $toggle.prop('disabled', false);

                if (response.success) {
                    // Update status badge
                    const statusBadge = $toggle.closest('tr').find('.status-badge');
                    if (newStatus === 1) {
                        statusBadge.removeClass('bg-secondary').addClass('bg-success').text('Aktif');
                    } else {
                        statusBadge.removeClass('bg-success').addClass('bg-secondary').text('Tidak Aktif');
                    }

                    // Show success toast
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });

                    Toast.fire({
                        icon: 'success',
                        title: 'Status berhasil diubah'
                    });
                } else {
                    // Revert toggle
                    $toggle.prop('checked', oldStatus === 1);

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Gagal mengubah status. Silakan coba lagi.'
                    });
                }
            },
            error: function(xhr) {
                $toggle.prop('disabled', false);
                $toggle.prop('checked', oldStatus === 1);

                let errorMsg = 'Gagal mengubah status. Silakan coba lagi.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMsg
                });
            }
        });
    });

    /**
     * Delete confirmation (SweetAlert)
     */
    $('.delete-btn').on('click', function(e) {
        e.preventDefault();

        const categoryId = $(this).data('category-id');
        const categoryName = $(this).data('category-name');
        const $row = $(this).closest('tr');

        Swal.fire({
            title: 'Konfirmasi Hapus',
            html: `Apakah Anda yakin ingin menghapus kategori <strong>${categoryName}</strong>?<br><br><small>Produk dalam kategori ini akan kehilangan kategorinya.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Menghapus...',
                    text: 'Mohon tunggu',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: BASE_URL + 'category/delete',
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        category_id: categoryId,
                        [CSRF_TOKEN_NAME]: CSRF_HASH
                    },
                    success: function(response) {
                        if (response.success) {
                            // Remove row from table
                            $row.fadeOut(300, function() {
                                $(this).remove();
                            });

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Kategori berhasil dihapus',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Gagal menghapus kategori'
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Gagal menghapus kategori. Silakan coba lagi.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMsg
                        });
                    }
                });
            }
        });
    });

    /**
     * Form submit loading state
     */
    $('form.category-form').on('submit', function() {
        const $submitBtn = $(this).find('button[type="submit"]');
        $submitBtn.prop('disabled', true);
        $submitBtn.html('<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...');
    });
});
