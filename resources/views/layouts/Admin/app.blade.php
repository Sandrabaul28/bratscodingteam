<!DOCTYPE html>
<html lang="en">
<head>
    <script type="text/javascript">
        window.history.forward();
    </script>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>{{ $title }}</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i') }}" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="{{ asset('assets/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('assets/img/LOGO2.png')}}" class="circular-logo">

     <!-- Include Bootstrap CSS if needed -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- para nis guide tour -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/shepherd.js@8.0.0/dist/css/shepherd.css">

    <style>
        body {
            overflow: hidden; /* Para hindi mag-scroll ang buong body */
        }
        #wrapper {
            height: 100vh; /* Set the wrapper to full height */
            display: flex;
        }
        #content {
            overflow-y: auto; /* Para sa scroll sa main content */
            flex: 1; /* This allows the content area to take the remaining space */
            padding: 20px; /* Optional: add some padding */
        }
        #sidebar {
            min-width: 250px; /* Minimum width of sidebar */
            max-width: 250px; /* Maximum width of sidebar */
            background: #4e73df; /* Sample sidebar background */
        }
    </style>

</head>
<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-success sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center">
                <div class="sidebar-brand-icon" style="display: flex; align-items: center; justify-content: center;">
                    <img src="{{ asset('assets/img/LOGO2.png') }}" alt="logo" style="width: 50px; height: 50px; border-radius: 50%;">
                </div>
                <div class="sidebar-brand-text" style="margin: 1px;">BONTOC CROPS</div> 
            </a>


            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href=" {{ route('admin.dashboard')}} ">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
                    
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Interface
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo" data-toggle="tooltip" title="Manage crop inventory">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Inventory</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">HVCDP Section:</h6>
                        <a class="collapse-item" href="{{ route('admin.count.count')}}" data-toggle="tooltip" title="Magdagdag ng bilang ng tanim at anong tanim ang meron sila">Crop Data Count</a>
                        <a class="collapse-item" href="{{ route('admin.hvcdp.index')}}" data-toggle="tooltip" title="Makikita ang lahat ng may records o wala">Crop Data Records</a>
                        <a class="collapse-item" href="{{ route('admin.inventory.index')}}" data-toggle="tooltip" title="Gumawa ng Monthly Report">Monthly Encoding</a>
                        <a class="collapse-item" href="{{ route('admin.inventory.history') }}" data-toggle="tooltip" title="Makikita ang mga records at changes mula sa Monthly Encoding">Monthly Records</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Account Management -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
                    aria-expanded="true" aria-controls="collapseUtilities" data-toggle="tooltip" title="Manage user accounts">
                    <i class="fas fa-fw fa-wrench"></i>
                    <span>Account Management</span>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="{{ route('admin.roles.createUser')}}" data-toggle="tooltip" title="Ang admin ay maaaring magdagdag ng Aggregator users">Admin User Management</a>
                        <a class="collapse-item" href="{{ route('admin.farmers.create') }}" data-toggle="tooltip" title="agdagdag ng Farmers na may account">Farmer Records</a>
                    </div>
                </div>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Database
            </div>

            <!-- Nav Item - Crops -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.plants.index')}}" data-toggle="tooltip" title="Magdagdag ng bagong tanim, kung wala pa sa database">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Plant Database</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.affiliations.index')}}" data-toggle="tooltip" title="Magdagdag ng bagong barangay o taong may sinalihang association">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Affiliation Directory</span>
                </a>
            </li>
            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Location
            </div>
            <!-- Nav Item - Location -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.map') }}" data-toggle="tooltip" title="Tingnan ang mapa ng lokasyon">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Map View</span>
                </a>
            </li>

            <!-- Activate Bootstrap Tooltip -->
            <script>
                $(document).ready(function () {
                    $('[data-toggle="tooltip"]').tooltip(); // Enable tooltip
                });
            </script>


            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <!-- End of Sidebar -->
        
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
    // Get the toggle button and sidebar
    const sidebarToggleButton = document.getElementById("sidebarToggleTop");
    const sidebar = document.getElementById("accordionSidebar");

    // Add event listener to toggle sidebar visibility
    sidebarToggleButton.addEventListener("click", function () {
        sidebar.classList.toggle("show");
    });
});

                    </script>

                    

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                               
                                <!-- Display user name on all screen sizes -->
                                <span class="mr-2 text-gray-600 small">
                                    {{ optional(auth()->user())->first_name }}
                                    <h6 style="font-size: 10px; margin: 0;">
                                        @if (strtoupper(optional(auth()->user()->role)->role_name) === 'ADMIN')
                                            ADMIN
                                        @elseif (strtoupper(optional(auth()->user()->role)->role_name) === 'AGGREGATOR')
                                            AGGREGATOR
                                        @else
                                            USER
                                        @endif
                                    </h6>
                                </span>

                                <div style="position: relative; display: inline-block;">
                                <img class="img-profile rounded-circle" 
                                     src="{{ asset('assets/img/undraw_profile.svg') }}" 
                                     alt="Profile Image" 
                                     style="display: block;">
                                <div style="
                                    position: absolute; 
                                    bottom: 0; 
                                    right: 0; 
                                    width: 15px; 
                                    height: 15px; 
                                    background-color: green; /* Green color for active status */ 
                                    border-radius: 50%; 
                                    border: 2px solid white; /* Optional: adds a white border around the indicator */
                                "></div>
                            </div>

                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->
                
                <!-- Begin Page Content -->
                <div class="container-fluid">
                    @yield('content')
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; SLSU - BONTOC 2024</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-danger" href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">
                        Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Shepherd.js JS -->
    <script src="https://cdn.jsdelivr.net/npm/shepherd.js@8.0.0/dist/js/shepherd.min.js"></script>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- Core plugin JavaScript-->
    <script src="{{ asset('assets/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <!-- Custom scripts for all pages-->
    <script src="{{ asset('assets/js/sb-admin-2.min.js') }}"></script>
    <!-- Page level plugins -->
    <script src="{{ asset('assets/vendor/chart.js/Chart.min.js') }}"></script>
    <!-- Page level custom scripts -->
    <script src="{{ asset('assets/js/demo/chart-area-demo.js') }}"></script>
    <script src="{{ asset('assets/js/demo/chart-pie-demo.js') }}"></script>
    

</body>
</html>
