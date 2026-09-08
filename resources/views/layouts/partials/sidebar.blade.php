<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        <a href="{{ route('dashboard') }}" class="brand-link">
            <span class="brand-text fw-light"><strong>Tracer</strong> Reporting</span>
        </a>
    </div>

    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-speedometer2"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('upload.create') }}" class="nav-link {{ request()->routeIs('upload.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-cloud-arrow-up"></i>
                        <p>Upload Data</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('analysis.index') }}" class="nav-link {{ request()->routeIs('analysis.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-bar-chart-line"></i>
                        <p>Hasil Analisis</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-file-earmark-word"></i>
                        <p>Laporan</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
