<ul class="py-1 menu-inner">
    <!-- Dashboard -->
    <li class="menu-item {{ Route::is('dashboard') ? 'active' : '' }}">
        <a href="{{ route('dashboard') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-home-circle"></i>
            <div data-i18n="Analytics">Dashboard</div>
        </a>
    </li>

    <li class="menu-item {{ Route::is('clients.index') ? 'active' : '' }}">
        <a href="{{ route('clients.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-group"></i>
            <div data-i18n="Analytics">Clients Registration</div>
        </a>
    </li>

    <li class="menu-item {{ Route::is('vendors.index') ? 'active' : '' }}">
        <a href="{{ route('vendors.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-user"></i>
            <div data-i18n="Vendors">Vendor Registration</div>
        </a>
    </li>

    <li class="menu-item {{ Route::is('search.projects.index') ? 'active' : '' }}">
        <a href="{{ route('search.projects.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-search"></i>
            <div data-i18n="projects">Search Projects/Details</div>
        </a>
    </li>

    <li class="menu-item {{ Route::is('current_projects.index') ? 'active' : '' }}">
        <a href="{{ route('current_projects.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-briefcase"></i>
            <div data-i18n="projects">Current Projects</div>
        </a>
    </li>

    <li class="menu-item {{ Route::is('open.quarter.projects.index') ? 'active' : '' }}">
        <a href="{{ route('open.quarter.projects.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-calendar-week"></i>
            <div data-i18n="Open Projects">Open Projects from Last Quarter</div>
        </a>
    </li>


    <li class="menu-item {{ Route::is('pending.projects.index') ? 'active' : '' }}">
        <a href="{{ route('pending.projects.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-history"></i>
            <div data-i18n="Pending Projects">Pending Invoices</div>
        </a>
    </li>

    <li class="menu-item {{ Route::is('closed.projects.index') ? 'active' : '' }}">
        <a href="{{ route('closed.projects.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-check-circle"></i>
            <div data-i18n="Pending Projects">Closed Projects</div>
        </a>
    </li>

    <li class="menu-item {{ Route::is('respondent.index') ? 'active' : '' }}">
        <a href="{{ route('respondent.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-user-check"></i>
            <div data-i18n="Pending Projects">Respondent Incentive Details</div>
        </a>
    </li>
    <li class="menu-item {{ Route::is('communication.index') ? 'active' : '' }}">
        <a href="{{ route('communication.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-message-detail"></i>
            <div data-i18n="Communication">Important Communication</div>
        </a>
    </li>

</ul>
<style>
    .bg-menu-theme .menu-inner>.menu-item.active:before {
        background: #0b5dbb !important;
    }

    .bg-menu-theme .menu-inner>.menu-item.active>.menu-link {
        color: #0b5dbb !important;
    }
</style>
