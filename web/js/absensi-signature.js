(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        initSignaturePad();
        initJabatanLainnya();
        initLookup();
    });

    function initSignaturePad() {
        var canvas = document.getElementById('signature-pad');
        var hiddenInput = document.getElementById('signature-data-input');
        var wrapper = document.getElementById('signature-wrapper');
        var errorBox = document.getElementById('signature-error');
        var form = document.getElementById('absensi-form');

        if (!canvas || !hiddenInput || typeof window.SignaturePad === 'undefined') {
            return;
        }

        var signaturePad = new window.SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)',
            penColor: 'rgb(20, 20, 20)',
            minWidth: 1,
            maxWidth: 2.5,
        });

        function resizeCanvas() {
            var ratio = Math.max(window.devicePixelRatio || 1, 1);
            var data = signaturePad.toData();

            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext('2d').scale(ratio, ratio);

            signaturePad.clear();
            if (data && data.length > 0) {
                signaturePad.fromData(data);
            }
            refreshState();
        }

        function syncHiddenInput() {
            hiddenInput.value = signaturePad.isEmpty() ? '' : signaturePad.toDataURL('image/png');
        }

        function refreshState() {
            var filled = !signaturePad.isEmpty();
            wrapper.classList.toggle('has-signature', filled);
            if (filled) {
                wrapper.classList.remove('is-invalid');
                if (errorBox) {
                    errorBox.textContent = '';
                }
            }
        }

        signaturePad.addEventListener('endStroke', function () {
            syncHiddenInput();
            refreshState();
        });

        ['mouseup', 'touchend', 'pointerup'].forEach(function (evt) {
            canvas.addEventListener(evt, function () {
                window.setTimeout(function () {
                    syncHiddenInput();
                    refreshState();
                }, 0);
            });
        });

        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();

        if (hiddenInput.value && hiddenInput.value.indexOf('data:image/png;base64,') === 0) {
            signaturePad.fromDataURL(hiddenInput.value, {
                width: canvas.offsetWidth,
                height: canvas.offsetHeight,
            });
            refreshState();
        }

        var clearBtn = document.getElementById('signature-clear');
        if (clearBtn) {
            clearBtn.addEventListener('click', function (e) {
                e.preventDefault();
                signaturePad.clear();
                hiddenInput.value = '';
                refreshState();
            });
        }

        if (form) {
            form.addEventListener('submit', function (e) {
                syncHiddenInput();

                if (signaturePad.isEmpty()) {
                    e.preventDefault();
                    e.stopPropagation();
                    wrapper.classList.add('is-invalid');
                    if (errorBox) {
                        errorBox.textContent = 'Tanda tangan digital wajib diisi.';
                    }
                    wrapper.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return false;
                }

                return true;
            });

            if (window.jQuery) {
                window.jQuery(form).on('beforeSubmit beforeValidate', function () {
                    syncHiddenInput();
                    return true;
                });
            }
        }
    }

    function initJabatanLainnya() {
        var select = document.getElementById('absensi-jabatan');
        var group = document.getElementById('jabatan-lainnya-group');

        if (!select || !group) {
            return;
        }

        function toggle() {
            var show = select.value === 'Lainnya';
            group.style.display = show ? '' : 'none';
            if (!show) {
                var input = document.getElementById('absensi-jabatan-lainnya');
                if (input) {
                    input.value = '';
                }
            }
        }

        select.addEventListener('change', toggle);
        toggle();
    }

    function initLookup() {
        var group = document.getElementById('lookup-group');
        var input = document.getElementById('lookup-input');
        var box = document.getElementById('lookup-suggestions');
        var hint = document.getElementById('lookup-hint');
        var hintText = document.getElementById('lookup-hint-text');

        if (!group || !input || !box) {
            return;
        }

        var url = group.getAttribute('data-lookup-url');
        var minChars = 3;
        var timer = null;
        var controller = null;
        var items = [];
        var activeIndex = -1;

        function showHint(message, muted) {
            if (!hint || !hintText) {
                return;
            }
            hintText.textContent = message;
            hint.classList.toggle('is-muted', !!muted);
            hint.hidden = false;
        }

        function hideHint() {
            if (hint) {
                hint.hidden = true;
            }
        }

        function closeBox() {
            box.hidden = true;
            box.innerHTML = '';
            activeIndex = -1;
        }

        function renderItems(list) {
            items = list;
            box.innerHTML = '';
            activeIndex = -1;

            if (list.length === 0) {
                var empty = document.createElement('div');
                empty.className = 'lookup-empty';
                empty.textContent = 'Data tidak ditemukan. Silakan isi manual di bawah.';
                box.appendChild(empty);
                box.hidden = false;
                return;
            }

            list.forEach(function (item, index) {
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'lookup-item';
                btn.setAttribute('data-index', String(index));

                var name = document.createElement('span');
                name.className = 'lookup-item-name';
                name.textContent = item.nama;

                var meta = document.createElement('span');
                meta.className = 'lookup-item-meta';
                meta.textContent = [item.identitas_number, item.jabatan, item.instansi]
                    .filter(Boolean)
                    .join(' \u00b7 ');

                btn.appendChild(name);
                if (meta.textContent !== '') {
                    btn.appendChild(meta);
                }

                btn.addEventListener('click', function () {
                    applyItem(item);
                });

                box.appendChild(btn);
            });

            box.hidden = false;
        }

        function setValue(id, value) {
            var el = document.getElementById(id);
            if (el && typeof value === 'string' && value !== '') {
                el.value = value;
                el.classList.remove('is-invalid');
            }
        }

        function setSelect(id, value) {
            var el = document.getElementById(id);
            if (!el || !value) {
                return false;
            }

            var found = Array.prototype.some.call(el.options, function (opt) {
                return opt.value === value;
            });

            if (found) {
                el.value = value;
                el.classList.remove('is-invalid');
            }

            return found;
        }

        function applyItem(item) {
            input.value = item.nama;

            setValue('absensi-nama', item.nama);
            setValue('absensi-identitas-number', item.identitas_number);
            setValue('absensi-instansi', item.instansi);
            setValue('absensi-email', item.email);

            if (!setSelect('absensi-tipe-identitas', item.tipe_identitas) && item.tipe_identitas) {
                setSelect('absensi-tipe-identitas', 'Lainnya');
            }

            if (item.jabatan) {
                if (!setSelect('absensi-jabatan', item.jabatan)) {
                    setSelect('absensi-jabatan', 'Lainnya');
                    setValue('absensi-jabatan-lainnya', item.jabatan);
                }
                var jabatanSelect = document.getElementById('absensi-jabatan');
                if (jabatanSelect) {
                    jabatanSelect.dispatchEvent(new Event('change'));
                }
            }

            showHint('Data ditemukan otomatis', false);
            closeBox();
        }

        function search(keyword) {
            if (controller) {
                controller.abort();
            }
            controller = typeof AbortController !== 'undefined' ? new AbortController() : null;

            var requestUrl = url + (url.indexOf('?') === -1 ? '?' : '&')
                + 'q=' + encodeURIComponent(keyword);

            fetch(requestUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                signal: controller ? controller.signal : undefined,
            })
                .then(function (response) {
                    return response.ok ? response.json() : { items: [] };
                })
                .then(function (data) {
                    renderItems((data && data.items) || []);
                })
                .catch(function (error) {
                    if (!error || error.name !== 'AbortError') {
                        closeBox();
                    }
                });
        }

        input.addEventListener('input', function () {
            var keyword = input.value.trim();
            hideHint();
            window.clearTimeout(timer);

            if (keyword.length < minChars) {
                closeBox();
                if (keyword.length > 0) {
                    showHint('Ketik minimal ' + minChars + ' huruf', true);
                }
                return;
            }

            timer = window.setTimeout(function () {
                search(keyword);
            }, 300);
        });

        input.addEventListener('keydown', function (e) {
            if (box.hidden || items.length === 0) {
                return;
            }

            var buttons = box.querySelectorAll('.lookup-item');

            if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                e.preventDefault();
                activeIndex += (e.key === 'ArrowDown' ? 1 : -1);
                if (activeIndex < 0) {
                    activeIndex = buttons.length - 1;
                }
                if (activeIndex >= buttons.length) {
                    activeIndex = 0;
                }
                Array.prototype.forEach.call(buttons, function (btn, i) {
                    btn.classList.toggle('is-active', i === activeIndex);
                });
            } else if (e.key === 'Enter') {
                if (activeIndex >= 0 && items[activeIndex]) {
                    e.preventDefault();
                    applyItem(items[activeIndex]);
                }
            } else if (e.key === 'Escape') {
                closeBox();
            }
        });

        document.addEventListener('click', function (e) {
            if (!group.contains(e.target)) {
                closeBox();
            }
        });
    }
}());
