<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ACCOUNT : Dashboard</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.hugeicons.com/font/icons.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        #preloader { transition: opacity 0.5s ease; }

        /* ══════════════════════════════════════════════
           SIDEBAR
        ══════════════════════════════════════════════ */
        #sidebar {
            width: 256px;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: visible;
            flex-shrink: 0;
        }
        #sidebar.collapsed { width: 68px; }

        #sidebar.collapsed .sidebar-logo-wrap {
            opacity: 0; max-width: 0;
            transform: translateX(-6px); pointer-events: none;
        }
        #sidebar .search-wrapper {
            transition: opacity 0.2s ease, max-height 0.3s ease, margin 0.3s ease, padding 0.3s ease;
            opacity: 1; max-height: 60px; overflow: hidden;
        }
        #sidebar.collapsed .search-wrapper {
            opacity: 0; max-height: 0;
            margin-top: 0 !important; pointer-events: none;
        }
        #sidebar .nav-label, #sidebar .nav-arrow {
            transition: opacity 0.15s ease, max-width 0.3s ease;
            opacity: 1; max-width: 180px; overflow: hidden; white-space: nowrap;
        }
        #sidebar.collapsed .nav-label, #sidebar.collapsed .nav-arrow {
            opacity: 0; max-width: 0; pointer-events: none;
        }
        #sidebar nav a { transition: padding 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        #sidebar nav a i.nav-icon { transition: margin 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        #sidebar.collapsed nav a { justify-content: center; padding-left: 0; padding-right: 0; }
        #sidebar.collapsed nav a i.nav-icon { margin-right: 0 !important; }
        #sidebar .sidebar-header { transition: padding 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        #sidebar.collapsed .sidebar-header {
            padding-left: 0; padding-right: 0; justify-content: center;
        }
        #sidebar.collapsed .sidebar-header .collapse-btn { margin: 0 auto; }
        #sidebar .sidebar-upgrade-card, #sidebar .sidebar-bottom-links {
            transition: opacity 0.2s ease, max-height 0.35s ease, transform 0.2s ease;
            opacity: 1; max-height: 300px; overflow: hidden;
        }
        #sidebar.collapsed .sidebar-upgrade-card {
            opacity: 0; max-height: 0; transform: scale(0.95); pointer-events: none;
        }
        .nav-active-dot {
            display: none; width: 6px; height: 6px; border-radius: 50%;
            background: #007682; position: absolute; right: 6px; top: 50%;
            transform: translateY(-50%);
        }
        #sidebar.collapsed .nav-active-dot { display: block; }
        #collapseBtn i { transition: transform 0.3s ease; }
        #sidebar.collapsed #collapseBtn i { transform: rotate(180deg); }

        /* Sidebar tooltip */
        #sidebar-tooltip {
            position: fixed; top: 0; left: 0;
            background: #1e293b; color: #fff;
            font-size: 11px; font-weight: 600;
            padding: 5px 11px; border-radius: 7px;
            white-space: nowrap; pointer-events: none; opacity: 0;
            transition: opacity 0.15s ease, transform 0.15s ease;
            transform: translateX(6px) translateY(-50%);
            z-index: 9999; will-change: transform, opacity;
        }
        #sidebar-tooltip.visible { opacity: 1; transform: translateX(0) translateY(-50%); }
        #sidebar-tooltip::before {
            content: ''; position: absolute; right: 100%; top: 50%;
            transform: translateY(-50%); border: 5px solid transparent;
            border-right-color: #1e293b;
        }
        @keyframes shimmer { 100% { transform: translateX(100%); } }
        #sidebar .sidebar-header a {
            transition: opacity 0.2s ease, max-width 0.3s ease, padding 0.3s ease;
            opacity: 1; max-width: 160px; overflow: hidden; flex-shrink: 0;
        }
        #sidebar.collapsed .sidebar-header a {
            opacity: 0; max-width: 0; padding: 0; pointer-events: none;
        }

        /* ══════════════════════════════════════════════
           SEARCH MODAL
        ══════════════════════════════════════════════ */

        /* Backdrop */
        #searchOverlay {
            position: fixed; inset: 0; z-index: 10000;
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(4px);
            display: none; align-items: flex-start; justify-content: center;
            padding-top: 80px;
        }
        #searchOverlay.open { display: flex; animation: smBackdropIn 0.15s ease; }
        @keyframes smBackdropIn { from { opacity: 0; } to { opacity: 1; } }

        /* Modal box */
        #searchModal {
            width: 100%; max-width: 660px;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 25px 60px -10px rgba(0,0,0,0.25), 0 0 0 1px rgba(0,0,0,0.07);
            overflow: hidden;
            display: flex; flex-direction: column;
            max-height: 74vh;
            animation: smSlideIn 0.18s cubic-bezier(0.34, 1.4, 0.64, 1);
        }
        @keyframes smSlideIn {
            from { opacity: 0; transform: translateY(-14px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0)     scale(1);    }
        }

        /* Input */
        #smInput {
            border: none; outline: none; background: transparent;
            font-size: 15px; font-weight: 500; color: #0f172a;
            width: 100%; caret-color: #007682;
        }
        #smInput::placeholder { color: #94a3b8; font-weight: 400; }

        /* Scrollable results */
        #smBody {
            overflow-y: auto; flex: 1;
            scrollbar-width: thin; scrollbar-color: #e2e8f0 transparent;
        }
        #smBody::-webkit-scrollbar { width: 4px; }
        #smBody::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }

        /* Filter tabs */
        .sm-tab {
            padding: 4px 14px; border-radius: 20px;
            font-size: 12px; font-weight: 600;
            border: 1.5px solid #e2e8f0; color: #64748b;
            cursor: pointer; white-space: nowrap; user-select: none;
            transition: all 0.12s; background: white;
        }
        .sm-tab.active { background: #007682; color: white; border-color: #007682; }
        .sm-tab:not(.active):hover { background: #f8fafc; border-color: #cbd5e1; }

        /* Section label */
        .sm-section-label {
            display: flex; align-items: center;
            font-size: 11px; font-weight: 700; letter-spacing: 0.06em;
            color: #94a3b8; text-transform: uppercase;
            padding: 10px 16px 4px;
        }
        .sm-count {
            font-size: 10px; font-weight: 700;
            background: #f0fafa; color: #007682;
            border-radius: 20px; padding: 1px 6px; margin-left: 5px;
        }

        /* Result row */
        .sm-row {
            display: flex; align-items: center; gap: 11px;
            padding: 9px 16px; cursor: pointer; position: relative;
            transition: background 0.1s;
            text-decoration: none; color: inherit;
        }
        .sm-row:hover, .sm-row.active-row { background: #f8fafc; }
        .sm-row.active-row::before {
            content: ''; position: absolute; left: 0; top: 50%;
            transform: translateY(-50%);
            width: 3px; height: 55%; border-radius: 0 3px 3px 0;
            background: #007682;
        }

        /* Action row */
        .sm-action {
            display: flex; align-items: center; justify-content: space-between;
            padding: 8px 16px; cursor: pointer; transition: background 0.1s;
            text-decoration: none; color: inherit;
        }
        .sm-action:hover, .sm-action.active-row { background: #f8fafc; }
        .sm-action-left { display: flex; align-items: center; gap: 11px; }
        .sm-action-icon {
            width: 30px; height: 30px; border-radius: 7px;
            background: #f1f5f9; display: grid; place-content: center;
            font-size: 13px; color: #64748b; flex-shrink: 0;
        }

        /* Recent row */
        .sm-recent {
            display: flex; align-items: center; justify-content: space-between;
            padding: 8px 16px; cursor: pointer; transition: background 0.1s;
        }
        .sm-recent:hover { background: #f8fafc; }
        .sm-recent-left { display: flex; align-items: center; gap: 11px; }
        .sm-recent-icon {
            width: 30px; height: 30px; border-radius: 7px;
            background: #f8fafc; border: 1px solid #e2e8f0;
            display: grid; place-content: center;
            font-size: 13px; color: #94a3b8; flex-shrink: 0;
        }

        /* Avatar circle */
        .sm-avatar {
            width: 34px; height: 34px; border-radius: 50%; flex-shrink: 0;
            display: grid; place-content: center;
            font-size: 12px; font-weight: 700; color: white;
            border: 1.5px solid rgba(255,255,255,0.25);
        }

        /* File icon box */
        .sm-file-icon {
            width: 34px; height: 34px; border-radius: 8px; flex-shrink: 0;
            display: grid; place-content: center; font-size: 15px;
        }

        /* Jump to btn */
        .sm-jump {
            display: none; align-items: center; gap: 4px;
            background: #f1f5f9; border: 1px solid #e2e8f0;
            border-radius: 7px; padding: 3px 10px;
            font-size: 11px; font-weight: 600; color: #007682; flex-shrink: 0;
            transition: background 0.1s;
        }
        .sm-row:hover .sm-jump, .sm-row.active-row .sm-jump,
        .sm-recent:hover .sm-jump { display: flex; }
        .sm-jump:hover { background: #e0f2f3; }

        /* Kbd */
        .sm-kbd {
            display: inline-flex; align-items: center;
            background: #f1f5f9; border: 1px solid #dde3ee;
            border-radius: 5px; padding: 2px 6px;
            font-size: 11px; font-weight: 600; color: #64748b; flex-shrink: 0;
        }

        /* See all */
        .sm-see-all {
            display: block; font-size: 12px; font-weight: 600;
            color: #007682; padding: 6px 16px; cursor: pointer;
            transition: background 0.1s;
            text-decoration: none;
        }
        .sm-see-all:hover { background: #f0fafa; }

        /* Divider */
        .sm-divider { height: 1px; background: #f1f5f9; margin: 3px 0; }

        /* Footer */
        #smFooter {
            border-top: 1px solid #f1f5f9; padding: 8px 16px;
            display: flex; align-items: center; gap: 14px;
            background: #fafafa; flex-shrink: 0;
        }
        .sm-foot { display: flex; align-items: center; gap: 5px; font-size: 11px; color: #94a3b8; }
        .sm-foot .sm-kbd { background: white; }

        /* Guest badge */
        .sm-guest {
            font-size: 10px; font-weight: 600; color: #64748b;
            background: #f1f5f9; border-radius: 4px;
            padding: 1px 5px; margin-left: 5px;
        }

        /* Highlight match text */
        .sm-match { color: #007682; font-weight: 700; }

        /* ── All Results expanded view ── */
        #smAllView {
            animation: smSlideIn 0.18s cubic-bezier(0.34, 1.4, 0.64, 1);
        }
        #smAllView .sm-row {
            border-bottom: 1px solid #f8fafc;
        }
    </style>
    @yield('customStyle')
</head>

<body class="h-full">

    <!-- Preloader -->
    <div id="preloader" class="fixed inset-0 z-50 flex items-center justify-center bg-white">
        <div class="w-12 h-12 border-4 border-slate-200 border-t-[#007682] rounded-full animate-spin"></div>
    </div>

    <!-- Body-level tooltip -->
    <div id="sidebar-tooltip"></div>

    <!-- ════════════════════════════════════════════════════════
         SEARCH MODAL
    ════════════════════════════════════════════════════════ -->
    <div id="searchOverlay" onclick="smHandleOverlayClick(event)">
      <div id="searchModal">

        <!-- Search bar -->
        <div class="flex items-center gap-3 px-4 py-3.5 border-b border-slate-100">
          <i class="hgi hgi-stroke hgi-search-01 text-slate-400 text-xl flex-shrink-0"></i>
          <input id="smInput" type="text"
                 placeholder="Type a command or search..."
                 oninput="smHandleInput(this.value)"
                 onkeydown="smHandleKey(event)"
                 autocomplete="off">
          <div class="flex items-center gap-2 flex-shrink-0">
            <span class="sm-kbd">⌘/</span>
            <button onclick="smClose()"
                    class="w-7 h-7 grid place-content-center rounded-lg hover:bg-slate-100 transition text-slate-400 hover:text-slate-600">
              <i class="hgi hgi-stroke hgi-cancel-01 text-sm"></i>
            </button>
          </div>
        </div>

        <!-- Filter tabs (visible only when searching) -->
        <div id="smFilterBar" class="hidden items-center gap-2 px-4 py-2.5 border-b border-slate-100 overflow-x-auto">
          <span class="text-xs text-slate-400 font-medium mr-1 flex-shrink-0">Filter by</span>
          <button class="sm-tab active" onclick="smSetFilter('all',this)">All</button>
          <button class="sm-tab" onclick="smSetFilter('contacts',this)">Contacts</button>
          <button class="sm-tab" onclick="smSetFilter('campaigns',this)">Campaigns</button>
          <button class="sm-tab" onclick="smSetFilter('templates',this)">Templates</button>
        </div>

        <!-- Body -->
        <div id="smBody">

          <!-- ── DEFAULT VIEW ── -->
          <div id="smDefaultView">
            <div class="sm-section-label" style="justify-content:space-between">
              Recent searches
            </div>

            <div class="sm-recent" onclick="smTriggerSearch('Email Campaign Q1')">
              <div class="sm-recent-left">
                <div class="sm-recent-icon"><i class="hgi hgi-stroke hgi-mail-open"></i></div>
                <span class="text-sm font-medium text-slate-700">Email Campaign Q1</span>
              </div>
              <div class="sm-jump"><i class="hgi hgi-stroke hgi-arrow-right-01 text-xs"></i>&nbsp;Jump to...</div>
            </div>
            <div class="sm-recent" onclick="smTriggerSearch('Newsletter Template')">
              <div class="sm-recent-left">
                <div class="sm-recent-icon"><i class="hgi hgi-stroke hgi-file-02"></i></div>
                <span class="text-sm font-medium text-slate-700">Newsletter Template</span>
              </div>
              <div class="sm-jump"><i class="hgi hgi-stroke hgi-arrow-right-01 text-xs"></i>&nbsp;Jump to...</div>
            </div>
            <div class="sm-recent" onclick="smTriggerSearch('March Contacts')">
              <div class="sm-recent-left">
                <div class="sm-recent-icon"><i class="hgi hgi-stroke hgi-at"></i></div>
                <span class="text-sm font-medium text-slate-700">March Contacts</span>
              </div>
              <div class="sm-jump"><i class="hgi hgi-stroke hgi-arrow-right-01 text-xs"></i>&nbsp;Jump to...</div>
            </div>

            <div class="sm-divider"></div>
            <div class="sm-section-label">Quick actions</div>

            <div class="sm-action" onclick="openCreateModal()">
              <div class="sm-action-left">
                <div class="sm-action-icon"><i class="hgi hgi-stroke hgi-mail-add-01"></i></div>
                <span class="text-sm font-medium text-slate-700">Create new campaign</span>
              </div>
              <span class="sm-kbd">⌘N</span>
            </div>
            <form method="post" action="{{ route('app.template.select') }}" class="m-0">
                @csrf
                <input name="id" value="6037a0a8583a7" hidden="">
                <input name="type" value="default" hidden="">
                <button type="submit" class="w-full">
                    <div class="sm-action">
                    <div class="sm-action-left">
                        <div class="sm-action-icon"><i class="hgi hgi-stroke hgi-file-add"></i></div>
                        <span class="text-sm font-medium text-slate-700">New template</span>
                    </div>
                    <span class="sm-kbd">⌘T</span>
                    </div>
                </button>
            </form>
            <a href="{{ route('app.report.index') }}" class="sm-action">
              <div class="sm-action-left">
                <div class="sm-action-icon"><i class="hgi hgi-stroke hgi-analytics-up"></i></div>
                <span class="text-sm font-medium text-slate-700">View reports</span>
              </div>
              <span class="sm-kbd">⌘R</span>
            </a>
            <a href="{{ route('app.setting.index') }}" class="sm-action">
              <div class="sm-action-left">
                <div class="sm-action-icon"><i class="hgi hgi-stroke hgi-settings-01"></i></div>
                <span class="text-sm font-medium text-slate-700">Settings</span>
              </div>
              <span class="sm-kbd">⌘,</span>
            </a>
            <a href="{{ route('app.support.index') }}" class="sm-action">
              <div class="sm-action-left">
                <div class="sm-action-icon"><i class="hgi hgi-stroke hgi-help-circle"></i></div>
                <span class="text-sm font-medium text-slate-700">Help center</span>
              </div>
              <span class="sm-kbd">⌘?</span>
            </a>
          </div>
          <!-- ── END DEFAULT VIEW ── -->

          <!-- ── RESULTS VIEW ── -->
          <div id="smResultsView" class="hidden">
            <!-- Contacts -->
            <div id="smSecContacts">
              <div class="sm-section-label">
                Contacts <span class="sm-count" id="smCntContacts">0</span>
              </div>
              <div id="smListContacts"></div>
              <a id="smSeeAllContacts" href="#" class="sm-see-all hidden">See all contact results →</a>
            </div>
            <div class="sm-divider" id="smDividerA"></div>
            <!-- Campaigns -->
            <div id="smSecCampaigns">
              <div class="sm-section-label">
                Campaigns <span class="sm-count" id="smCntCampaigns">0</span>
              </div>
              <div id="smListCampaigns"></div>
              <a id="smSeeAllCampaigns" href="#" class="sm-see-all hidden">See all campaign results →</a>
            </div>
            <div class="sm-divider" id="smDividerB"></div>
            <!-- Templates -->
            <div id="smSecTemplates">
              <div class="sm-section-label">
                Templates <span class="sm-count" id="smCntTemplates">0</span>
              </div>
              <div id="smListTemplates"></div>
              <a id="smSeeAllTemplates" href="#" class="sm-see-all hidden">See all template results →</a>
            </div>
          </div>
          <!-- ── END RESULTS VIEW ── -->

          <!-- ── ALL RESULTS EXPANDED VIEW ── -->
          <div id="smAllView" class="hidden">
            <div class="flex items-center gap-3 px-4 py-3 border-b border-slate-100 bg-white sticky top-0 z-10 shadow-[0_1px_3px_rgba(0,0,0,0.04)]">
              <button onclick="smShowResultsFromAll()"
                      class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100
                             hover:bg-slate-200 text-xs font-bold text-slate-600
                             hover:text-slate-800 transition-all active:scale-95">
                <i class="hgi hgi-stroke hgi-arrow-left-01 text-xs"></i>
                Back
              </button>
              <span id="smAllSectionTitle" class="text-xs font-bold text-slate-700 tracking-wide uppercase"></span>
              <span id="smAllCountBadge"
                    class="text-[10px] font-bold bg-[#007682]/10 text-[#007682]
                           rounded-full px-2.5 py-0.5 ml-auto border border-[#007682]/15"></span>
            </div>
            <div id="smAllList"></div>
          </div>
          <!-- ── END ALL RESULTS VIEW ── -->

          <!-- ── EMPTY VIEW ── -->
          <div id="smEmptyView" class="hidden text-center py-12 px-8">
            <div class="w-12 h-12 rounded-full bg-slate-100 grid place-content-center mx-auto mb-3">
              <i class="hgi hgi-stroke hgi-search-01 text-xl text-slate-400"></i>
            </div>
            <p class="text-sm font-semibold text-slate-600 mb-1">No results found</p>
            <p class="text-xs text-slate-400">Try a different search term</p>
          </div>
        </div>
        <!-- end smBody -->

        <!-- Footer -->
        <div id="smFooter">
          <div class="sm-foot"><span class="sm-kbd">↑</span><span class="sm-kbd">↓</span> navigate</div>
          <div class="sm-foot"><span class="sm-kbd">↵</span> open</div>
          <div class="sm-foot"><span class="sm-kbd">esc</span> close</div>
          <div class="sm-foot ml-auto">
            <i class="hgi hgi-stroke hgi-search-01 text-xs"></i> Search
          </div>
        </div>

      </div>
    </div>
    <!-- ════════════ END SEARCH MODAL ════════════ -->


    <div class="flex h-screen overflow-hidden">

        <!-- ══════════════════════ SIDEBAR ══════════════════════ -->
        <aside id="sidebar" class="flex flex-col bg-white border-r border-slate-200 shadow-sm">

            <!-- Logo + Collapse -->
            <div class="sidebar-header flex items-center justify-between pl-6 pr-4 h-16 border-slate-200">
                <a href="/" class="block flex-shrink-0 py-3">
                    <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="h-7 w-full">
                </a>
                <button id="collapseBtn" title="Toggle Sidebar"
                    class="collapse-btn hover:bg-slate-100 transition grid place-content-center group rounded-lg h-8 w-8 flex-shrink-0">
                    <i class="hgi hgi-stroke hgi-sidebar-left-01 text-xl text-slate-400 group-hover:text-slate-600"></i>
                </button>
            </div>

            <!-- Search trigger -->
            <div class="search-wrapper px-4 mt-2">
                <button onclick="smOpen()"
                    class="w-full flex items-center bg-slate-50 border border-slate-200 rounded-lg px-3 py-2
                           hover:border-slate-300 hover:bg-white transition-all group text-left">
                    <i class="hgi hgi-stroke hgi-search-01 text-slate-400 mr-2 text-lg flex-shrink-0"></i>
                    <span class="flex-1 text-sm text-slate-400 group-hover:text-slate-500">Search anything...</span>
                    <span class="hidden md:flex items-center gap-1 bg-white border border-slate-200 px-1.5 py-0.5
                                 rounded text-[10px] text-slate-400 font-bold flex-shrink-0">
                        <i class="hgi hgi-stroke hgi-command"></i>K
                    </span>
                </button>
            </div>

            <!-- Nav -->
            <nav class="flex-1 px-4 mt-6 overflow-y-auto overflow-x-hidden space-y-1">
                @php
                    $navs = [
                        ['route' => 'app.dashboard',      'icon' => 'hgi-home-01',      'label' => 'Dashboard'],
                        ['route' => 'app.campaign.index', 'icon' => 'hgi-mail-open',    'label' => 'Campaigns'],
                        ['route' => 'app.group.index',    'icon' => 'hgi-at',           'label' => 'Contacts'],
                        ['route' => 'app.report.index',   'icon' => 'hgi-analytics-up', 'label' => 'Reports'],
                        ['route' => 'app.template.index', 'icon' => 'hgi-file-02',      'label' => 'Templates'],
                        ['route' => 'app.form.index',     'icon' => 'hgi-file-02',      'label' => 'Forms'],
                        ['route' => 'app.setting.index',  'icon' => 'hgi-settings-01',  'label' => 'Settings'],
                    ];
                    $isActive = fn($route) => match($route) {
                        'app.group.index' => request()->routeIs('app.group.index')
                                          || request()->routeIs('app.contact.*')
                                          || request()->is('app/contacts/*'),
                        default => request()->routeIs($route),
                    };
                @endphp

                @foreach($navs as $nav)
                    @php $active = $isActive($nav['route']); @endphp
                    <a href="{{ route($nav['route']) }}"
                       data-tooltip="{{ $nav['label'] }}"
                       class="relative flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all group
                              {{ $active ? 'bg-teal-50 text-[#007682]' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="nav-icon hgi hgi-stroke {{ $nav['icon'] }} text-xl flex-shrink-0 mr-3
                                  {{ $active ? 'text-[#007682]' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                        <span class="nav-label flex-grow">{{ $nav['label'] }}</span>
                        <i class="nav-arrow hgi hgi-stroke hgi-arrow-right-01 text-xs transition-opacity
                                  {{ $active ? 'opacity-100' : 'opacity-0 group-hover:opacity-100' }}"></i>
                        @if($active)<span class="nav-active-dot"></span>@endif
                    </a>
                @endforeach
            </nav>

            <!-- Bottom -->
            <div class="p-4 mt-auto border-t border-slate-100 space-y-4">
                @if(false)
                <div class="sidebar-upgrade-card bg-gradient-to-br from-[#007682] to-[#408b86] rounded-xl p-4 text-white shadow-lg relative overflow-hidden group">
                    <div class="relative z-10 flex justify-between items-center mb-3">
                        <h4 class="font-bold tracking-tight leading-none capitalize">Free Account</h4>
                    </div>
                    <div class="relative z-10 flex flex-col gap-1.5 mb-4">
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-teal-50/70">Remaining Emails</span>
                            <span class="font-bold">0</span>
                        </div>
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-teal-50/70">Remaining Group</span>
                            <span class="font-bold">0</span>
                        </div>
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-teal-50/70">Remaining Campaigns</span>
                            <span class="font-bold">0</span>
                        </div>
                    </div>
                    @if(false)
                    <div class="relative z-10">
                        <button onclick="openUpgradeModal()"
                            class="flex items-center justify-center w-full bg-white text-[#007682] py-2 rounded-lg text-xs font-black hover:bg-teal-50 transition-all active:scale-95 shadow-sm">
                            SELECT PLAN <i class="hgi hgi-stroke hgi-arrow-right-01 ml-2 text-[10px]"></i>
                        </button>
                    </div>
                    @endif
                </div>
                @endif
                <div class="sidebar-bottom-links space-y-1">
                    <a href="{{ url('../contact-us') }}" data-tooltip="Feedback"
                       class="flex items-center px-3 py-2 text-xs font-medium text-slate-500 hover:bg-slate-50 rounded-lg">
                        <i class="hgi hgi-stroke hgi-chat mr-3 text-lg flex-shrink-0"></i>
                        <span class="nav-label">Feedback</span>
                    </a>
                    <a href="{{ route('app.support.index') }}" data-tooltip="Help Center"
                       class="flex items-center px-3 py-2 text-xs font-medium text-slate-500 hover:bg-slate-50 rounded-lg">
                        <i class="hgi hgi-stroke hgi-help-circle mr-3 text-lg flex-shrink-0"></i>
                        <span class="nav-label">Help Center</span>
                    </a>
                </div>
            </div>
        </aside>
        <!-- ══ END SIDEBAR ══ -->

        <!-- Main content area -->
        <div class="flex-1 flex flex-col min-w-0">

            <header class="h-16 flex items-center justify-between px-8 bg-white/80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-40">
                <div class="flex items-center space-x-3 text-sm">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:gap-3">
                        <span class="text-slate-800 text-[11px] sm:text-sm font-semibold">Dashboard</span>
                        <span class="text-slate-600">|</span>
                        <span class="text-slate-600 text-[11px] sm:text-sm font-medium current_time">Loading time...</span>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    @if(false)
                        <a href="{{ url('../pricing') }}"
                           class="flex items-center gap-2 px-4 py-2 bg-[#1c7f84] rounded-full group hover:bg-[#16666a] transition-all active:scale-95 shadow-sm">
                            <i class="hgi hgi-stroke hgi-star text-white text-xs animate-pulse"></i>
                            <span class="text-[11px] font-bold text-white uppercase tracking-wider">Free Account</span>
                        </a>
                    @else
                        <div class="relative overflow-hidden flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-amber-500 via-yellow-400 to-amber-500 rounded-full border border-white/30">
                            <div class="absolute inset-0 -translate-x-full animate-[shimmer_2.5s_infinite] bg-gradient-to-r from-transparent via-white/40 to-transparent"></div>
                            <i class="hgi hgi-solid hgi-crown text-white text-[10px] drop-shadow-md"></i>
                            <span class="relative text-[11px] font-black text-white uppercase tracking-widest">Free Account</span>
                        </div>
                    @endif

                    <div class="relative group">
                        <button type="button"
                            class="flex items-center gap-2 p-0.5 rounded-full hover:bg-slate-50 transition-all duration-300">
                            <div class="relative">
                                <img class="w-8 h-8 rounded-full object-cover shadow-sm border border-slate-300 ring-1 ring-slate-100"
                                     src="{{ asset('assets/img/blank_avatar.jpg') }}" alt="avatar">
                                <div class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-green-500 border-2 border-white rounded-full"></div>
                            </div>
                            <i class="hgi hgi-stroke hgi-arrow-down-01 text-slate-400 text-xs group-hover:rotate-180 transition-transform duration-300"></i>
                        </button>
                        <div class="absolute border right-0 mt-3 w-52 opacity-0 invisible group-hover:opacity-100 group-hover:visible translate-y-2 group-hover:translate-y-0 transition-all duration-200 z-50">
                            <div class="bg-white rounded-xl shadow-[0_10px_30px_-10px_rgba(0,0,0,0.15)] border border-slate-100 overflow-hidden">
                                <div class="px-4 py-3 bg-slate-50/50 border-b">
                                    <p class="text-[12px] font-bold text-slate-800 mb-1 truncate">
                                        {{ auth()->user()->name }}
                                    </p>
                                    <p class="text-xs font-bold text-slate-500 truncate">{{ auth()->user()->email }}</p>
                                </div>
                                <div class="p-1.5">
                                    <a href="{{ route('app.account.index') }}"
                                       class="flex items-center gap-2.5 px-2.5 py-2 text-[13px] font-medium text-slate-600 hover:text-teal-600 hover:bg-teal-50 rounded-lg transition-all group/item">
                                        <i class="hgi hgi-stroke hgi-user-circle text-base text-slate-400 group-hover/item:text-teal-600"></i> My Profile
                                    </a>
                                    <a href="{{ route('app.setting.index') }}"
                                       class="flex items-center gap-2.5 px-2.5 py-2 text-[13px] font-medium text-slate-600 hover:text-teal-600 hover:bg-teal-50 rounded-lg transition-all group/item">
                                        <i class="hgi hgi-stroke hgi-settings-02 text-base text-slate-400 group-hover/item:text-teal-600"></i> Settings
                                    </a>
                                    <a onclick="logout()" href="#"
                                       class="flex items-center gap-2.5 px-2.5 py-2 text-[13px] font-medium text-slate-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all group/logout">
                                        <i class="hgi hgi-stroke hgi-logout-circle-01 text-base text-slate-400 group-hover/logout:text-red-600"></i> Logout
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-8 bg-slate-50/50">
                <div class="max-w-7xl mx-auto relative">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Upgrade Modal -->
    <div id="upgradeModal"
         class="fixed inset-0 z-50 items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm hidden">
        <div class="bg-white rounded-2xl w-full max-w-md p-8 shadow-2xl">
            <h3 class="text-xl font-bold text-slate-900 mb-2">Would you like to upgrade now?</h3>
            <p class="text-slate-600 text-sm leading-relaxed mb-8">
                Get more from HybridMail. Upgrade to the
                <span class="font-bold uppercase">{{ auth()->user()->role == 1 ? 'standard' : 'enterprise' }} account</span>
                now and increase your monthly emails, contacts, and campaigns.
            </p>
            <div class="flex flex-col gap-3">
                <a href="#"
                   class="w-full bg-[#007682] text-white py-2.5 rounded-xl font-bold text-center hover:bg-[#005f69] transition-colors">
                    Upgrade Your Account
                </a>
                <button onclick="closeModal()" class="w-full text-slate-400 text-sm font-semibold hover:text-slate-600">Maybe Later</button>
            </div>
        </div>
    </div>

    <!-- Create Campaign Modal -->
    <div id="createModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/20 backdrop-blur-sm p-4">
        <div class="bg-gradient-to-br from-blue-50 via-blue-50/50 to-teal-50/30 w-full max-w-lg rounded-xl shadow-2xl overflow-hidden relative">
            <div class="relative bg-white backdrop-blur-xl rounded-2xl shadow-lg border">
                <div class="text-center pt-14 pb-12 px-8 bg-gradient-to-b from-[#007682]/20 via-[#408b86]/10 to-white">
                    <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm">
                        <i class="hgi hgi-stroke hgi-user-multiple-02 text-2xl text-teal-600"></i>
                    </div>
                    <h2 class="text-xl font-bold text-slate-900 mb-2">Create New Campaign</h2>
                    <p class="text-sm text-slate-500 leading-relaxed">
                        Share your latest news, promote products, or announce an upcoming event to your subscribers.
                    </p>
                </div>

                <form method="post" action="{{ route('app.campaign.store') }}" class="px-8 pb-8">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Campaign Name</label>
                        <input
                            type="text"
                            name="name"
                            maxlength="100"
                            required
                            placeholder="e.g. Summer Flash Sale 2026"
                            class="w-full px-4 py-3.5 rounded-xl border border-slate-200 bg-white focus:border-teal-400 focus:ring-4 focus:ring-teal-500/10 outline-none transition text-slate-700 placeholder:text-slate-400"
                        />
                        <p class="mt-2 text-xs text-slate-500">
                            0 slots remaining this month.
                            @if(0 <= 3)
                                <a href="#" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-700 transition">
                                    Upgrade plan
                                    <i class="hgi hgi-stroke hgi-arrow-right-01"></i>
                                </a>
                            @endif
                        </p>
                    </div>
                    <div class="flex gap-3">
                        <button type="button" onclick="closeCreateModal()"
                            class="flex-1 py-3 rounded-xl bg-slate-100 text-slate-700 text-sm font-bold hover:bg-slate-200 transition">
                            Cancel
                        </button>
                        <button type="submit"
                            {{0 <= 0 ? 'disabled': ''}}
                            class="bg-gradient-to-br from-[#007682] to-[#408b86] hover:brightness-110 flex-1 py-3 rounded-xl text-white text-sm font-bold hover:shadow-[#007682]/30 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 flex items-center justify-center gap-2">
                            <i class="hgi hgi-stroke hgi-mail-send-01"></i>
                            <span>Create Campaign</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>

    // ── Create Modal ──
    function openCreateModal() {
        smClose();
        document.getElementById('createModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeCreateModal() {
        document.getElementById('createModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    $(document).ready(function () {

        /* ── Preloader ── */
        setTimeout(() => { $('#preloader').addClass('opacity-0 pointer-events-none'); }, 800);

        /* ── Clock ── */
        function updateTime() {
            const now = new Date();
            $(".current_time").text(new Intl.DateTimeFormat('en-GB', {
                timeZone: "Europe/London",
                weekday: "long", day: "2-digit", month: "short", year: "numeric",
                hour: "2-digit", minute: "2-digit", hour12: true
            }).format(now));
        }
        updateTime();
        setInterval(updateTime, 60000);

        /* ── Sidebar collapse ── */
        const STORAGE_KEY = 'sidebar_collapsed';
        const $sidebar = $('#sidebar');
        const $btn = $('#collapseBtn');

        if (localStorage.getItem(STORAGE_KEY) === 'true') $sidebar.addClass('collapsed');

        $btn.on('click', function () {
            const isCollapsed = $sidebar.toggleClass('collapsed').hasClass('collapsed');
            localStorage.setItem(STORAGE_KEY, isCollapsed);
            hideTooltip();
        });

        const $tip = $('#sidebar-tooltip');
        let hideTimer = null;
        function showTooltip($el) {
            if (!$sidebar.hasClass('collapsed')) return;
            const label = $el.data('tooltip');
            if (!label) return;
            const rect = $el[0].getBoundingClientRect();
            $tip.text(label).css({ left: (rect.right + 12) + 'px', top: (rect.top + rect.height / 2) + 'px' }).addClass('visible');
        }
        function hideTooltip() { $tip.removeClass('visible'); }
        $sidebar.find('nav a[data-tooltip]')
            .on('mouseenter', function () { clearTimeout(hideTimer); showTooltip($(this)); })
            .on('mouseleave', function () { hideTimer = setTimeout(hideTooltip, 100); });
    });

    /* ── Upgrade modal ── */
    function openUpgradeModal() { $('#upgradeModal').removeClass('hidden').addClass('flex'); }
    function closeModal()       { $('#upgradeModal').addClass('hidden').removeClass('flex'); }

    /* ── Logout ── */
    function logout() {
        localStorage.setItem('loggedInDuration', 0);
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ route('logout') }}";
        form.style.display = 'none';
        var csrf = document.createElement('input');
        csrf.name = '_token';
        csrf.value = "{{ csrf_token() }}";
        form.appendChild(csrf);
        document.body.appendChild(form);
        form.submit();
    }


    /* ════════════════════════════════════════════════════════
       SEARCH MODAL LOGIC
    ════════════════════════════════════════════════════════ */

    let smFilter   = 'all';
    let smRows     = [];
    let smRowIndex = -1;
    let smLastRawQ = '';

    // Stores the FULL dataset from the last search (used by "see all")
    let _smAllData = { contacts: [], campaigns: [], templates: [], q: '' };

    /* ── Open ── */
    function smOpen() {
        document.getElementById('searchOverlay').classList.add('open');
        setTimeout(() => document.getElementById('smInput').focus(), 60);
    }

    /* ── Close ── */
    function smClose() {
        document.getElementById('searchOverlay').classList.remove('open');
        document.getElementById('smInput').value = '';
        smLastRawQ = '';
        smShowDefault();
        smFilter = 'all';
        document.querySelectorAll('.sm-tab').forEach((t, i) => t.classList.toggle('active', i === 0));
    }

    /* ── Click outside ── */
    function smHandleOverlayClick(e) {
        if (e.target === document.getElementById('searchOverlay')) smClose();
    }

    /* ── Global keyboard shortcuts ── */
    document.addEventListener('keydown', function (e) {
        if ((e.metaKey || e.ctrlKey) && e.key === 'k') { e.preventDefault(); smOpen(); }
        if ((e.metaKey || e.ctrlKey) && e.key === '/') { e.preventDefault(); smOpen(); }
        if (e.key === 'Escape' && document.getElementById('searchOverlay').classList.contains('open')) smClose();
    });

    /* ── Filter tabs ── */
    function smSetFilter(filter, btn) {
        smFilter = filter;
        document.querySelectorAll('.sm-tab').forEach(t => t.classList.remove('active'));
        btn.classList.add('active');
        smHandleInput(document.getElementById('smInput').value);
    }

    /* ── Debounce ── */
    let smDebounceTimer = null;
    function smHandleInput(q) {
        clearTimeout(smDebounceTimer);
        smDebounceTimer = setTimeout(() => smDoSearch(q), 220);
    }

    /* ── Core search ── */
    function smDoSearch(q) {
        q = q.trim();
        smLastRawQ = q;

        if (!q) { smShowDefault(); return; }

        const fb = document.getElementById('smFilterBar');
        fb.classList.remove('hidden'); fb.classList.add('flex');

        fetch('/app/search?q=' + encodeURIComponent(q) + '&filter=' + encodeURIComponent(smFilter), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            const lower = q.toLowerCase();
            const fc = (smFilter === 'all' || smFilter === 'contacts')  ? (data.contacts  || []) : [];
            const fm = (smFilter === 'all' || smFilter === 'campaigns') ? (data.campaigns || []) : [];
            const ft = (smFilter === 'all' || smFilter === 'templates') ? (data.templates || []) : [];

            if (!fc.length && !fm.length && !ft.length) { smShowEmpty(); return; }

            smRenderResults(fc, fm, ft, lower);
        })
        .catch(() => smShowEmpty());
    }

    /* ── Trigger search from recent item ── */
    function smTriggerSearch(term) {
        document.getElementById('smInput').value = term;
        smDoSearch(term);
    }

    /* ── Keyboard navigation ── */
    function smHandleKey(e) {
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            smRows[smRowIndex]?.classList.remove('active-row');
            smRowIndex = Math.min(smRowIndex + 1, smRows.length - 1);
            smRows[smRowIndex]?.classList.add('active-row');
            smRows[smRowIndex]?.scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            smRows[smRowIndex]?.classList.remove('active-row');
            smRowIndex = Math.max(smRowIndex - 1, 0);
            smRows[smRowIndex]?.classList.add('active-row');
            smRows[smRowIndex]?.scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'Enter' && smRowIndex >= 0) {
            const row = smRows[smRowIndex];
            if (row) {
                if (row.tagName === 'A' && row.href) {
                    window.location.href = row.href;
                } else {
                    row.click();
                }
            }
        }
    }

    /* ── Views ── */
    function smShowDefault() {
        document.getElementById('smDefaultView').classList.remove('hidden');
        document.getElementById('smResultsView').classList.add('hidden');
        document.getElementById('smEmptyView').classList.add('hidden');
        document.getElementById('smAllView').classList.add('hidden');
        const fb = document.getElementById('smFilterBar');
        fb.classList.add('hidden'); fb.classList.remove('flex');
        smRows = []; smRowIndex = -1;
    }
    function smShowEmpty() {
        document.getElementById('smDefaultView').classList.add('hidden');
        document.getElementById('smResultsView').classList.add('hidden');
        document.getElementById('smEmptyView').classList.remove('hidden');
        document.getElementById('smAllView').classList.add('hidden');
        smRows = []; smRowIndex = -1;
    }

    /* ── Render results (first 3 per section + "See all" button) ── */
    function smRenderResults(contacts, campaigns, templates, q) {
        // Cache the full dataset for "see all" expansion
        _smAllData = { contacts, campaigns, templates, q };

        document.getElementById('smDefaultView').classList.add('hidden');
        document.getElementById('smEmptyView').classList.add('hidden');
        document.getElementById('smAllView').classList.add('hidden');
        document.getElementById('smResultsView').classList.remove('hidden');

        // ── Contacts ──
        const secC = document.getElementById('smSecContacts');
        const divA = document.getElementById('smDividerA');
        const btnC = document.getElementById('smSeeAllContacts');
        if (!contacts.length) {
            secC.style.display = 'none';
            divA.style.display = 'none';
        } else {
            secC.style.display = '';
            document.getElementById('smCntContacts').textContent = contacts.length;
            document.getElementById('smListContacts').innerHTML = contacts.slice(0, 3).map(c => smContactRow(c, q)).join('');
            divA.style.display = (campaigns.length || templates.length) ? '' : 'none';
            if (contacts.length > 3) {
                btnC.textContent = 'See all ' + contacts.length + ' contact results →';
                btnC.classList.remove('hidden');
                btnC.onclick = function (e) { e.preventDefault(); smOpenAllView('contacts'); };
            } else {
                btnC.classList.add('hidden');
            }
        }

        // ── Campaigns ──
        const secM = document.getElementById('smSecCampaigns');
        const divB = document.getElementById('smDividerB');
        const btnM = document.getElementById('smSeeAllCampaigns');
        if (!campaigns.length) {
            secM.style.display = 'none';
            divB.style.display = 'none';
        } else {
            secM.style.display = '';
            document.getElementById('smCntCampaigns').textContent = campaigns.length;
            document.getElementById('smListCampaigns').innerHTML = campaigns.slice(0, 3).map(c => smCampaignRow(c, q)).join('');
            divB.style.display = templates.length ? '' : 'none';
            if (campaigns.length > 3) {
                btnM.textContent = 'See all ' + campaigns.length + ' campaign results →';
                btnM.classList.remove('hidden');
                btnM.onclick = function (e) { e.preventDefault(); smOpenAllView('campaigns'); };
            } else {
                btnM.classList.add('hidden');
            }
        }

        // ── Templates ──
        const secT = document.getElementById('smSecTemplates');
        const btnT = document.getElementById('smSeeAllTemplates');
        if (!templates.length) {
            secT.style.display = 'none';
        } else {
            secT.style.display = '';
            document.getElementById('smCntTemplates').textContent = templates.length;
            document.getElementById('smListTemplates').innerHTML = templates.slice(0, 3).map(t => smTemplateRow(t, q)).join('');
            if (templates.length > 3) {
                btnT.textContent = 'See all ' + templates.length + ' template results →';
                btnT.classList.remove('hidden');
                btnT.onclick = function (e) { e.preventDefault(); smOpenAllView('templates'); };
            } else {
                btnT.classList.add('hidden');
            }
        }

        smRows     = [...document.querySelectorAll('#smResultsView .sm-row')];
        smRowIndex = -1;
    }

    /* ══════════════════════════════════════════════
       "SEE ALL" — INLINE EXPANDED VIEW
    ══════════════════════════════════════════════ */

    function smOpenAllView(section) {
        const { contacts, campaigns, templates, q } = _smAllData;

        // Swap views
        document.getElementById('smResultsView').classList.add('hidden');
        document.getElementById('smAllView').classList.remove('hidden');

        // Set header title & badge
        const titles = { contacts: 'Contacts', campaigns: 'Campaigns', templates: 'Templates' };
        document.getElementById('smAllSectionTitle').textContent = titles[section] || section;

        let items = [];
        let html  = '';

        if (section === 'contacts') {
            items = contacts;
            html  = items.map(c => smContactRow(c, q)).join('');
        } else if (section === 'campaigns') {
            items = campaigns;
            html  = items.map(c => smCampaignRow(c, q)).join('');
        } else if (section === 'templates') {
            items = templates;
            html  = items.map(t => smTemplateRow(t, q)).join('');
        }

        document.getElementById('smAllCountBadge').textContent =
            items.length + ' result' + (items.length !== 1 ? 's' : '');

        document.getElementById('smAllList').innerHTML =
            html || '<p class="text-sm text-slate-400 px-4 py-8 text-center">No items found.</p>';

        // Scroll to top
        document.getElementById('smBody').scrollTop = 0;

        // Rebuild keyboard nav
        smRows     = [...document.querySelectorAll('#smAllView .sm-row')];
        smRowIndex = -1;
    }

    function smShowResultsFromAll() {
        document.getElementById('smAllView').classList.add('hidden');
        document.getElementById('smResultsView').classList.remove('hidden');
        document.getElementById('smBody').scrollTop = 0;

        smRows     = [...document.querySelectorAll('#smResultsView .sm-row')];
        smRowIndex = -1;
    }

    /* ══════════════════════════════════════════════
       ROW RENDER HELPERS
    ══════════════════════════════════════════════ */

    function smHL(q, str) {
        if (!q || !str) return str || '';
        return String(str).replace(
            new RegExp('(' + q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi'),
            '<span class="sm-match">$1</span>'
        );
    }

    function smContactRow(c, q) {
        const guest    = c.guest ? `<span class="sm-guest">Guest</span>` : '';
        const href     = c.url || '#';
        const groupId  = c.group_id || '';
        const safeName = c.name.replace(/\\/g, '\\\\').replace(/'/g, "\\'");
        return `
        <div class="sm-row py-3 group/crow" id="sm-contact-row-${c.id}">
          <a href="${href}" class="flex items-center gap-3 flex-1 min-w-0">
            <div class="sm-avatar flex-shrink-0" style="background:${c.color}">${c.initials}</div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center text-sm font-semibold text-slate-800">${smHL(q, c.name)}</div>
              <div class="text-xs text-slate-500 truncate">${smHL(q, c.email)}</div>
            </div>
          </a>
        </div>`;
    }

    function smCampaignRow(c, q) {
        return `
        <a href="${c.url}" class="sm-row">
          <div class="sm-file-icon" style="background:${c.bgColor}">
            <i class="hgi hgi-stroke ${c.icon}" style="color:${c.iconColor}"></i>
          </div>
          <div class="flex-1 min-w-0">
            <div class="text-sm font-semibold text-slate-800 truncate">${smHL(q, c.name)}</div>
            <div class="text-xs text-slate-400">${c.date}</div>
          </div>
          <div class="sm-jump"><i class="hgi hgi-stroke hgi-arrow-right-01 text-xs"></i>&nbsp;Open</div>
        </a>`;
    }

    function smTemplateRow(t, q) {
        return `
        <a href="${t.url}" class="sm-row">
          <div class="sm-file-icon" style="background:${t.bgColor}">
            <i class="hgi hgi-stroke ${t.icon}" style="color:${t.iconColor}"></i>
          </div>
          <div class="flex-1 min-w-0">
            <div class="text-sm font-semibold text-slate-800 truncate">${smHL(q, t.name)}</div>
            <div class="text-xs text-slate-400">${t.date}</div>
          </div>
          <div class="sm-jump"><i class="hgi hgi-stroke hgi-arrow-right-01 text-xs"></i>&nbsp;Open</div>
        </a>`;
    }
    </script>
    @yield('script')
</body>
</html>
