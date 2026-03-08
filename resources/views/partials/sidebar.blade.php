 <div class="startbar d-print-none">
     <!--start brand-->
     <div class="brand">
         <a href="index.html" class="logo">
             <span>
                 <img src="{{ asset('/assets/images/logo_green.png') }}" alt="logo-small" loading="lazy" style="width: 100px; height: auto;">
             </span>
             {{-- <span class="">
                    <img src="assets/images/logo-light.png" alt="logo-large" class="logo-lg logo-light">
                    <img src="assets/images/logo-dark.png" alt="logo-large" class="logo-lg logo-dark">
                </span> --}}
         </a>
     </div>
     <div class="startbar-menu">
         <div class="startbar-collapse" id="startbarCollapse" data-simplebar>
             <div class="d-flex align-items-start flex-column w-100">
                 <ul class="navbar-nav mb-auto w-100">
                     <li class="menu-label pt-0 mt-0">
                         <span>Main Menu</span>
                     </li>
                     <li class="nav-item">
                         <a class="nav-link" href="{{route("dashboard")}}" role="button"
                             aria-expanded="false" aria-controls="sidebarDashboards">
                             <i class="iconoir-home-simple menu-icon"></i>
                             <span>Dashboards</span>
                         </a>
                     </li>
                     <li class="nav-item">
                         <a class="nav-link" href="{{route("staff")}}" aria-expanded="false"
                             aria-controls="sidebarApplications">
                             <i class="fas fa-user-friends menu-icon"></i>
                             <span>Staff management</span>
                         </a>
                     </li>
                     <li class="nav-item">
                         <a class="nav-link" href="{{route('supplier.show')}}" aria-expanded="false"
                             aria-controls="sidebarApplications">
                             <i class="fas fa-truck menu-icon"></i>
                             <span>Suppliers</span>
                         </a>
                     </li>
                     <li class="nav-item">
                         <a class="nav-link" href="{{route("devices")}}" role="button"
                             aria-expanded="false" aria-controls="sidebarAdvancedUI">
                             <i class="fas fa-desktop menu-icon"></i>
                             <span>Connected devices</span>
                         </a>
                     </li>
                     <li class="nav-item">
                         <a class="nav-link" href="{{route('inventory.show')}}" role="button"
                             aria-expanded="false" aria-controls="sidebarForms">
                             <i class="iconoir-journal-page menu-icon"></i>
                             <span>Inventory mangement</span>
                         </a>
                     </li>
                     <li class="nav-item">
                         <a class="nav-link" href="{{route('order')}}" role="button"
                             aria-expanded="false" aria-controls="sidebarForms">
                             <i class="far fa-clipboard menu-icon"></i>
                             <span>Orders</span>
                         </a>
                     </li>
                     <li class="nav-item">
                         <a class="nav-link" href="{{route('expenses')}}" role="button"
                             aria-expanded="false" aria-controls="sidebarForms">
                             <i class="fas fa-clipboard menu-icon"></i>
                             <span>Expenses</span>
                         </a>
                     </li>
                     <!-- <li class="nav-item">
                         <a class="nav-link" href="#sidebarCharts" data-bs-toggle="collapse" role="button"
                             aria-expanded="false" aria-controls="sidebarCharts">
                             <i class="iconoir-candlestick-chart menu-icon"></i>
                             <span>Manage prices flow</span>
                         </a>
                         <div class="collapse " id="sidebarCharts">
                             <ul class="nav flex-column">
                                 <li class="nav-item">
                                     <a class="nav-link" href="charts-justgage.html">Edit Prices</a>
                                 </li>
                                 <li class="nav-item">
                                     <a class="nav-link" href="charts-justgage.html">Price Flow</a>
                                 </li><!-
                                     </ul>
                         </div>
                     </li> -->
                     <li class="nav-item">
                         <a class="nav-link" href="#sidebarTables" data-bs-toggle="collapse" role="button"
                             aria-expanded="false" aria-controls="sidebarTables">
                             <i class="iconoir-table-rows menu-icon"></i>
                             <span>Records and Reports</span>
                         </a>
                         <div class="collapse " id="sidebarTables">
                             <ul class="nav flex-column">
                                 <li class="nav-item">
                                     <a class="nav-link" href="{{route('sales')}}">Sales</a>
                                 </li>
                                 <li class=" nav-item">
                                     <a class="nav-link" href="{{route('user')}}">User Activity</a>
                                 </li>
                                 <li class=" nav-item">
                                     <a class="nav-link" href="{{route('stock')}}">Stock</a>
                                 </li>
                                 <li class="nav-item">
                                     <a class="nav-link" href="{{route('finance')}}">Financial</a>
                                 </li>
                                 <li class="nav-item">
                                     <a class="nav-link" href="{{route('purchases')}}">Purchases</a>
                                 </li>
                             </ul>
                         </div>
                     </li>
                     <li class="nav-item">
                         <a class="nav-link" href="#sidebarCharts" data-bs-toggle="collapse" role="button"
                             aria-expanded="false" aria-controls="sidebarCharts">
                             <i class="fas fa-sync menu-icon"></i>
                             <span>Sync to Server</span>
                         </a>
                         <div class="collapse " id="sidebarCharts">
                             <ul class="nav flex-column">
                                 <li class="nav-item">
                                     <a class="nav-link" href="{{route('sync')}}">Sync Summary</a>
                                 </li>
                                 <li class="nav-item">
                                     <a class="nav-link" href="{{route('sync_finance')}}">Sync Finance</a>
                                 </li>
                                 <li class="nav-item">
                                     <a class="nav-link" href="{{route('sync_purchases')}}">Sync Purchases</a>
                                 </li>
                                 <li class="nav-item">
                                     <a class="nav-link" href="{{route('sync_sales')}}">Sync Sales</a>
                                 </li>
                                 <li class="nav-item">
                                     <a class="nav-link" href="{{route('sync_stock')}}">Sync Stock</a>
                                 </li>
                                 <li class="nav-item">
                                     <a class="nav-link" href="{{route('sync_users')}}">Sync Users</a>
                                 </li>
                             </ul>
                         </div>
                     </li>
                 </ul>
             </div>
         </div>
     </div>
     <div class="startbar-overlay d-print-none"></div>
 </div>