<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Klink Report')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --app-bg-top: #f8fbff;
            --app-bg-bottom: #eaf1f8;
            --ink: #1b2230;
            --muted: #6b7280;
            --sidebar-width: 234px;
            --sidebar-collapsed-width: 84px;
            --sidebar-top: #18294d;
            --sidebar-bottom: #0e1730;
            --sidebar-text: #edf2fb;
            --sidebar-muted: rgba(205, 214, 229, 0.72);
            --sidebar-line: rgba(255, 255, 255, 0.08);
            --accent: #3192ff;
            --accent-strong: #1869ec;
            --accent-soft: rgba(49, 146, 255, 0.2);
            --card: rgba(255, 255, 255, 0.9);
            --font-regular: 500;
            --font-semibold: 600;
            --font-bold: 700;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Quicksand", "Trebuchet MS", sans-serif;
            font-weight: var(--font-regular);
            color: var(--ink);
            background:
                radial-gradient(circle at top left, rgba(24, 105, 236, 0.16), transparent 28%),
                radial-gradient(circle at bottom right, rgba(8, 145, 178, 0.14), transparent 22%),
                linear-gradient(180deg, var(--app-bg-top) 0%, var(--app-bg-bottom) 100%);
            overflow-x: hidden;
        }

        button,
        input,
        select,
        textarea {
            font: inherit;
        }

        .sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: var(--sidebar-width);
            min-height: 100vh;
            padding: 14px 12px;
            color: var(--sidebar-text);
            background: linear-gradient(180deg, var(--sidebar-top) 0%, var(--sidebar-bottom) 100%);
            border-right: 1px solid var(--sidebar-line);
            box-shadow: 16px 0 48px rgba(15, 23, 42, 0.16);
            overflow-y: auto;
            transition: width 180ms ease, padding 180ms ease;
        }

        .sidebar::before {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background:
                radial-gradient(circle at 12% 8%, rgba(151, 194, 255, 0.28), transparent 24%),
                radial-gradient(circle at 88% 88%, rgba(34, 197, 94, 0.12), transparent 18%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.05), transparent 22%);
        }

        .sidebar-inner {
            position: relative;
            z-index: 1;
            display: flex;
            min-height: calc(100vh - 28px);
            flex-direction: column;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 12px;
            border: 1px solid var(--sidebar-line);
            border-radius: 18px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.09), rgba(255, 255, 255, 0.04));
            backdrop-filter: blur(14px);
        }

        .sidebar-header-main {
            display: flex;
            min-width: 0;
            align-items: center;
            gap: 10px;
        }

        .brand-mark {
            display: inline-flex;
            height: 40px;
            width: 40px;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            background: radial-gradient(circle at 30% 30%, #a8ccff 0%, #5ba7ff 38%, #1d5ee0 100%);
            box-shadow:
                inset 0 0 0 1px rgba(255, 255, 255, 0.18),
                0 12px 24px rgba(29, 94, 224, 0.28);
        }

        .brand-mark svg {
            width: 20px;
            height: 20px;
            fill: none;
            stroke: white;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-width: 1.9;
        }

        .brand-kicker,
        .sidebar-section-title,
        .sidebar-footer-title {
            display: block;
            font-size: 0.62rem;
            font-weight: var(--font-semibold);
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: var(--sidebar-muted);
        }

        .brand-copy h2 {
            margin: 0;
            font-size: 1rem;
            line-height: 1.25;
            font-weight: var(--font-bold);
            color: #f8fafc;
        }

        .brand-copy p {
            margin: 4px 0 0;
            color: rgba(226, 234, 246, 0.72);
            font-size: 0.7rem;
            line-height: 1.55;
        }

        .sidebar-toggle {
            display: inline-flex;
            height: 32px;
            width: 32px;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.06);
            color: var(--sidebar-text);
            cursor: pointer;
            transition: background 180ms ease, transform 180ms ease;
        }

        .sidebar-toggle:hover {
            background: rgba(255, 255, 255, 0.12);
        }

        .sidebar-toggle svg {
            width: 16px;
            height: 16px;
            fill: none;
            stroke: currentColor;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-width: 2;
            transition: transform 180ms ease;
        }

        .sidebar-section {
            margin-top: 16px;
        }

        .sidebar-section-title {
            padding: 0 6px;
            margin-bottom: 8px;
        }

        .sidebar-menu {
            display: grid;
            gap: 6px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            border: 1px solid transparent;
            border-radius: 14px;
            background: transparent;
            color: var(--sidebar-text);
            font-weight: var(--font-semibold);
            text-decoration: none;
            transition:
                transform 180ms ease,
                background 180ms ease,
                border-color 180ms ease,
                box-shadow 180ms ease;
        }

        .sidebar-link:hover {
            transform: translateX(2px);
            border-color: rgba(255, 255, 255, 0.08);
            background: rgba(255, 255, 255, 0.06);
            color: #f8fbff;
        }

        .sidebar-link.is-active {
            border-color: rgba(220, 236, 255, 0.22);
            background: linear-gradient(135deg, #2b7ff1, #1759d4);
            box-shadow: 0 18px 36px rgba(24, 105, 236, 0.3);
            color: #ffffff;
        }

        .sidebar-link.is-disabled {
            opacity: 0.86;
        }

        .sidebar-link.is-disabled:hover {
            transform: none;
            background: rgba(255, 255, 255, 0.04);
            cursor: not-allowed;
        }

        .sidebar-group {
            display: grid;
            gap: 8px;
        }

        .sidebar-link-group {
            width: 100%;
            cursor: pointer;
            appearance: none;
            text-align: left;
        }

        .sidebar-link-group:hover {
            transform: translateX(2px);
        }

        .sidebar-icon {
            display: inline-flex;
            height: 34px;
            width: 34px;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 11px;
            background: rgba(255, 255, 255, 0.07);
        }

        .sidebar-link.is-active .sidebar-icon {
            background: rgba(255, 255, 255, 0.16);
            border-color: rgba(255, 255, 255, 0.18);
        }

        .sidebar-icon svg {
            width: 16px;
            height: 16px;
            fill: none;
            stroke: currentColor;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-width: 1.9;
        }

        .sidebar-link-content {
            display: flex;
            width: 100%;
            min-width: 0;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .sidebar-link-label {
            display: block;
            font-size: 0.85rem;
            font-weight: var(--font-semibold);
            color: inherit;
        }

        .sidebar-group-caret {
            display: inline-flex;
            height: 18px;
            width: 18px;
            align-items: center;
            justify-content: center;
            color: inherit;
            opacity: 0.76;
        }

        .sidebar-group-caret svg {
            width: 14px;
            height: 14px;
            fill: none;
            stroke: currentColor;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-width: 2;
            transition: transform 180ms ease;
        }

        .sidebar-group.is-open .sidebar-group-caret svg {
            transform: rotate(180deg);
        }

        .sidebar-link:hover .sidebar-group-caret,
        .sidebar-link.is-active .sidebar-group-caret {
            opacity: 1;
        }

        .sidebar-submenu {
            display: grid;
            gap: 6px;
            margin-left: 16px;
            padding-left: 16px;
            border-left: 1px solid rgba(255, 255, 255, 0.08);
        }

        .sidebar-submenu[hidden] {
            display: none !important;
        }

        .sidebar-sublink {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            min-height: 36px;
            padding: 8px 10px;
            border-radius: 12px;
            color: rgba(237, 242, 251, 0.82);
            font-size: 0.78rem;
            font-weight: var(--font-semibold);
            text-decoration: none;
            transition: background 180ms ease, color 180ms ease, transform 180ms ease;
        }

        .sidebar-sublink:hover {
            transform: translateX(2px);
            background: rgba(255, 255, 255, 0.05);
            color: #f8fbff;
        }

        .sidebar-sublink.is-active {
            background: rgba(49, 146, 255, 0.14);
            color: #ffffff;
        }

        .sidebar-subdot {
            width: 7px;
            height: 7px;
            flex-shrink: 0;
            border-radius: 999px;
            background: rgba(160, 196, 255, 0.52);
            box-shadow: 0 0 0 4px rgba(49, 146, 255, 0.08);
        }

        .sidebar-sublink.is-active .sidebar-subdot {
            background: #8fd3ff;
            box-shadow: 0 0 0 4px rgba(143, 211, 255, 0.16);
        }

        .sidebar-sublink-label {
            min-width: 0;
            color: inherit;
        }

        .sidebar-badge {
            flex-shrink: 0;
            padding: 5px 8px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
            color: rgba(255, 255, 255, 0.74);
            font-size: 0.56rem;
            font-weight: var(--font-bold);
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .sidebar-footer {
            margin-top: auto;
            padding: 12px;
            border: 1px solid var(--sidebar-line);
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(12px);
        }

        .sidebar-footer strong {
            display: block;
            margin-top: 6px;
            color: #f4f7fb;
            font-size: 0.9rem;
            font-weight: var(--font-bold);
            line-height: 1.35;
        }

        .sidebar-user-card {
            margin-top: auto;
            padding: 14px;
            border: 1px solid var(--sidebar-line);
            border-radius: 18px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0.04));
            backdrop-filter: blur(12px);
        }

        .sidebar-user-top {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-user-avatar {
            display: inline-flex;
            height: 42px;
            width: 42px;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            background: radial-gradient(circle at 30% 30%, #b7d5ff 0%, #66a9ff 42%, #2563eb 100%);
            color: #ffffff;
            font-size: 0.9rem;
            font-weight: var(--font-bold);
            letter-spacing: 0.06em;
            box-shadow: 0 12px 24px rgba(37, 99, 235, 0.22);
        }

        .sidebar-user-meta {
            min-width: 0;
        }

        .sidebar-user-meta strong {
            display: block;
            color: #f8fbff;
            font-size: 0.86rem;
            line-height: 1.3;
        }

        .sidebar-user-meta span,
        .sidebar-user-handle {
            display: block;
            color: rgba(226, 234, 246, 0.72);
            font-size: 0.72rem;
            line-height: 1.55;
        }

        .sidebar-user-foot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-top: 12px;
        }

        .sidebar-user-handle {
            min-width: 0;
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .sidebar-logout {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 34px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 0 12px;
            background: rgba(255, 255, 255, 0.08);
            color: #f8fbff;
            font-size: 0.74rem;
            font-weight: var(--font-bold);
            cursor: pointer;
            transition: background 180ms ease, transform 180ms ease;
        }

        .sidebar-logout:hover {
            transform: translateY(-1px);
            background: rgba(255, 255, 255, 0.14);
        }

        .main-content {
            min-height: 100vh;
            margin-left: var(--sidebar-width);
            padding: 22px;
            transition: margin-left 180ms ease;
        }

        .main-content > * {
            max-width: 1440px;
        }

        body.has-confirm-open {
            overflow: hidden;
        }

        .confirm-modal {
            position: fixed;
            inset: 0;
            z-index: 1400;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .confirm-modal[hidden] {
            display: none !important;
        }

        .confirm-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(15, 23, 42, 0.46);
            backdrop-filter: blur(6px);
        }

        .confirm-dialog {
            position: relative;
            width: min(440px, 100%);
            padding: 24px;
            border: 1px solid rgba(148, 163, 184, 0.2);
            border-radius: 24px;
            background:
                radial-gradient(circle at top right, rgba(49, 146, 255, 0.1), transparent 30%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(246, 249, 255, 0.98));
            box-shadow:
                0 28px 64px rgba(15, 23, 42, 0.24),
                inset 0 1px 0 rgba(255, 255, 255, 0.6);
        }

        .confirm-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 14px;
            padding: 7px 11px;
            border-radius: 999px;
            background: rgba(254, 226, 226, 0.88);
            color: #b91c1c;
            font-size: 0.7rem;
            font-weight: var(--font-bold);
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .confirm-kicker svg {
            width: 14px;
            height: 14px;
            fill: none;
            stroke: currentColor;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-width: 1.9;
        }

        .confirm-dialog h3 {
            margin: 0;
            font-size: 1.2rem;
            font-weight: var(--font-bold);
            color: #0f172a;
            line-height: 1.35;
        }

        .confirm-dialog p {
            margin: 10px 0 0;
            color: #64748b;
            font-size: 0.9rem;
            line-height: 1.7;
        }

        .confirm-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 22px;
        }

        .confirm-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 124px;
            min-height: 42px;
            padding: 0 16px;
            border: 1px solid transparent;
            border-radius: 14px;
            font-size: 0.84rem;
            font-weight: var(--font-bold);
            cursor: pointer;
            transition: transform 180ms ease, box-shadow 180ms ease, background 180ms ease, border-color 180ms ease;
        }

        .confirm-btn:hover {
            transform: translateY(-1px);
        }

        .confirm-btn.secondary {
            border-color: rgba(148, 163, 184, 0.3);
            background: rgba(248, 250, 252, 0.96);
            color: #334155;
        }

        .confirm-btn.secondary:hover {
            background: rgba(241, 245, 249, 1);
        }

        .confirm-btn.primary {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: #ffffff;
            box-shadow: 0 18px 36px rgba(220, 38, 38, 0.22);
        }

        .confirm-btn.primary:hover {
            box-shadow: 0 22px 40px rgba(220, 38, 38, 0.28);
        }

        .filter-submit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            white-space: nowrap;
        }

        .filter-submit svg {
            width: 15px;
            height: 15px;
            fill: none;
            stroke: currentColor;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-width: 2;
            flex-shrink: 0;
        }

        body.sidebar-collapsed .sidebar {
            width: var(--sidebar-collapsed-width);
            padding: 14px 10px;
        }

        body.sidebar-collapsed .main-content {
            margin-left: var(--sidebar-collapsed-width);
        }

        body.sidebar-collapsed .brand-copy,
        body.sidebar-collapsed .sidebar-section-title,
        body.sidebar-collapsed .sidebar-badge,
        body.sidebar-collapsed .sidebar-user-card,
        body.sidebar-collapsed .sidebar-footer {
            display: none;
        }

        body.sidebar-collapsed .sidebar-header {
            padding: 10px 8px;
        }

        body.sidebar-collapsed .sidebar-header-main {
            gap: 0;
        }

        body.sidebar-collapsed .sidebar-link {
            justify-content: center;
            padding: 10px 0;
        }

        body.sidebar-collapsed .sidebar-link-content {
            display: none;
        }

        body.sidebar-collapsed .sidebar-submenu {
            display: none;
        }

        body.sidebar-collapsed .sidebar-toggle svg {
            transform: rotate(180deg);
        }

        @media (max-width: 960px) {
            .sidebar {
                position: relative;
                width: 100%;
                min-height: auto;
                border-right: none;
                border-bottom: 1px solid rgba(255, 255, 255, 0.08);
                box-shadow: none;
            }

            .sidebar-inner {
                min-height: auto;
            }

            .main-content {
                margin-left: 0;
                padding: 20px;
            }

            body.sidebar-collapsed .sidebar {
                width: 100%;
                padding: 14px 12px;
            }

            body.sidebar-collapsed .main-content {
                margin-left: 0;
            }

            body.sidebar-collapsed .brand-copy,
            body.sidebar-collapsed .sidebar-section-title,
            body.sidebar-collapsed .sidebar-user-card,
            body.sidebar-collapsed .sidebar-footer {
                display: block;
            }

            body.sidebar-collapsed .sidebar-badge {
                display: inline-flex;
            }

            body.sidebar-collapsed .sidebar-link-content {
                display: flex;
            }

            body.sidebar-collapsed .sidebar-submenu {
                display: grid;
            }

            body.sidebar-collapsed .sidebar-header {
                padding: 12px;
            }

            body.sidebar-collapsed .sidebar-link {
                justify-content: flex-start;
                padding: 10px;
            }
        }

        @media (max-width: 640px) {
            .sidebar {
                padding: 12px 10px;
            }

            .sidebar-header {
                padding: 10px;
            }

            .brand-mark {
                height: 36px;
                width: 36px;
                border-radius: 12px;
            }

            .sidebar-link {
                padding: 10px;
            }

            .main-content {
                padding: 16px;
            }
        }
    </style>
</head>
<body>

    @include('components.sidebar')

    <main class="main-content">
        @yield('content')
    </main>

    <div class="confirm-modal" id="globalConfirmModal" hidden aria-hidden="true">
        <div class="confirm-backdrop" data-confirm-close></div>

        <div class="confirm-dialog" role="dialog" aria-modal="true" aria-labelledby="confirmModalTitle" aria-describedby="confirmModalDescription">
            <span class="confirm-kicker">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 9v4"></path>
                    <path d="M12 17h.01"></path>
                    <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"></path>
                </svg>
                Konfirmasi Hapus
            </span>
            <h3 id="confirmModalTitle">Hapus data ini?</h3>
            <p id="confirmModalDescription">Data yang sudah dihapus tidak bisa dikembalikan lagi.</p>

            <div class="confirm-actions">
                <button type="button" class="confirm-btn secondary" data-confirm-close>Batal</button>
                <button type="button" class="confirm-btn primary" id="confirmModalAccept">Ya, hapus data</button>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const body = document.body;
            const toggle = document.querySelector('[data-sidebar-toggle]');
            const sidebarGroups = Array.from(document.querySelectorAll('[data-sidebar-group]'));
            const storageKey = 'klink-report.sidebar-collapsed';
            const groupStorageKey = 'klink-report.sidebar-groups';
            const desktopMedia = window.matchMedia('(min-width: 961px)');

            const readGroupState = () => {
                try {
                    return JSON.parse(window.localStorage.getItem(groupStorageKey) ?? '{}');
                } catch (error) {
                    return {};
                }
            };

            const writeGroupState = (state) => {
                window.localStorage.setItem(groupStorageKey, JSON.stringify(state));
            };

            const setGroupOpen = (group, isOpen) => {
                const submenu = group.querySelector('[data-sidebar-submenu]');
                const toggleButton = group.querySelector('[data-sidebar-group-toggle]');

                group.classList.toggle('is-open', isOpen);
                toggleButton?.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

                if (submenu) {
                    submenu.hidden = !isOpen;
                }
            };

            const syncState = () => {
                if (!desktopMedia.matches) {
                    body.classList.remove('sidebar-collapsed');
                    return;
                }

                if (window.localStorage.getItem(storageKey) === '1') {
                    body.classList.add('sidebar-collapsed');
                } else {
                    body.classList.remove('sidebar-collapsed');
                }
            };

            syncState();

            toggle?.addEventListener('click', () => {
                if (!desktopMedia.matches) {
                    return;
                }

                body.classList.toggle('sidebar-collapsed');
                window.localStorage.setItem(storageKey, body.classList.contains('sidebar-collapsed') ? '1' : '0');
            });

            desktopMedia.addEventListener('change', syncState);

            const savedGroups = readGroupState();

            sidebarGroups.forEach((group) => {
                const groupId = group.dataset.sidebarGroup;
                const toggleButton = group.querySelector('[data-sidebar-group-toggle]');
                const isActive = group.classList.contains('is-active');
                const isOpen = Object.prototype.hasOwnProperty.call(savedGroups, groupId)
                    ? Boolean(savedGroups[groupId])
                    : isActive;

                setGroupOpen(group, isOpen);

                toggleButton?.addEventListener('click', () => {
                    const nextOpen = !group.classList.contains('is-open');
                    const nextState = readGroupState();

                    setGroupOpen(group, nextOpen);
                    nextState[groupId] = nextOpen;
                    writeGroupState(nextState);
                });
            });

            const confirmModal = document.getElementById('globalConfirmModal');
            const confirmTitle = document.getElementById('confirmModalTitle');
            const confirmDescription = document.getElementById('confirmModalDescription');
            const confirmAccept = document.getElementById('confirmModalAccept');
            const confirmCloseButtons = Array.from(document.querySelectorAll('[data-confirm-close]'));
            const confirmForms = Array.from(document.querySelectorAll('form[data-confirm-delete]'));
            let pendingForm = null;
            let lastFocusedElement = null;

            const closeConfirmModal = () => {
                if (!confirmModal || confirmModal.hidden) {
                    pendingForm = null;
                    return;
                }

                confirmModal.hidden = true;
                confirmModal.setAttribute('aria-hidden', 'true');
                body.classList.remove('has-confirm-open');
                pendingForm = null;
                lastFocusedElement?.focus?.();
            };

            const openConfirmModal = (form) => {
                if (!confirmModal) {
                    return;
                }

                pendingForm = form;
                lastFocusedElement = document.activeElement;
                confirmTitle.textContent = form.dataset.confirmTitle || 'Hapus data ini?';
                confirmDescription.textContent = form.dataset.confirmMessage || 'Data yang sudah dihapus tidak bisa dikembalikan lagi.';
                confirmAccept.textContent = form.dataset.confirmButton || 'Ya, hapus data';
                confirmModal.hidden = false;
                confirmModal.setAttribute('aria-hidden', 'false');
                body.classList.add('has-confirm-open');
                window.setTimeout(() => confirmAccept.focus(), 20);
            };

            confirmForms.forEach((form) => {
                form.addEventListener('submit', (event) => {
                    event.preventDefault();
                    openConfirmModal(form);
                });
            });

            confirmCloseButtons.forEach((button) => {
                button.addEventListener('click', closeConfirmModal);
            });

            confirmAccept?.addEventListener('click', () => {
                if (!pendingForm) {
                    closeConfirmModal();
                    return;
                }

                const formToSubmit = pendingForm;
                closeConfirmModal();
                HTMLFormElement.prototype.submit.call(formToSubmit);
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeConfirmModal();
                }
            });
        })();
    </script>

</body>
</html>
