<!-- topbar ends -->
<div class="ch-container">
    <div class="row">

        <!-- left menu starts -->
        <div class="col-sm-2 col-lg-2">
            <div class="sidebar-nav">
                <div class="nav-canvas">
                    <div class="nav-sm nav nav-stacked">

                    </div>
                    <ul class="nav nav-pills nav-stacked main-menu">
                        <li class="nav-header">Main</li>
                        <li><a class="ajax-link" href="./"><i class="glyphicon glyphicon-home"></i><span> Dashboard</span></a></li>
                        <li class="accordion">
                            <a href="#"><i class="glyphicon glyphicon-plus"></i><span> APIs Details</span></a>
                            <ul class="nav nav-pills nav-stacked">
                                <li class="nav-header">Dashboard</li>
                                <li><?php echo $this->Html->link("Location Widgets", array('controller' => 'home', 'action' => 'index/locationwidgets'), array('escape' => false)); ?></li>
                                <li><?php echo $this->Html->link("Create Widget", array('controller' => 'home', 'action' => 'index/createwidget'), array('escape' => false)); ?></li>
                                <li class="nav-header">User</li>
                                <li><?php echo $this->Html->link("Login", array('controller' => 'home', 'action' => 'index/login'), array('escape' => false)); ?></li>
                                <li><?php echo $this->Html->link("Forgot Password", array('controller' => 'home', 'action' => 'index/forgotpassword'), array('escape' => false)); ?></li>
                                <li><?php echo $this->Html->link("Change Password", array('controller' => 'home', 'action' => 'index/changepassword'), array('escape' => false)); ?></li>
                                <li><?php echo $this->Html->link("Logout", array('controller' => 'home', 'action' => 'index/logout'), array('escape' => false)); ?></li>
                                <li><?php echo $this->Html->link("Logout All Sessions", array('controller' => 'home', 'action' => 'index/logoutallsessions'), array('escape' => false)); ?></li>
                            </ul>
                        </li>
                    </ul>

                </div>
            </div>
        </div>
        <!--/span-->
        <!-- left menu ends -->

        <div id="content" class="col-lg-10 col-sm-10">

            <div class="row">
                <div class="box col-md-12">
                    <div class="box-inner">
                        <div class="box-header well">
                            <h2><i class="glyphicon glyphicon-info-sign"></i> Introduction</h2>

                            <div class="box-icon">
                                <a href="#" class="btn btn-minimize btn-round btn-default">
                                    <i class="glyphicon glyphicon-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="box-content row">
                            <div class="col-lg-12 col-md-12">
                                <h4><?= $apiType ?></h4>
                                <p> <?= $content ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- content ends -->
        </div><!--/#content.col-md-0-->
    </div><!--/fluid-row-->


    <hr>

    <footer class="row">
        <p class="col-md-9 col-sm-9 col-xs-12 copyright">&copy; <a href="https://rudrainnovatives.com/" target="_blank">Rudra Innovatives
            </a> @ <?php echo date("Y"); ?></p>

        <p class="col-md-3 col-sm-3 col-xs-12 powered-by">Powered by: <a
                href="./">Smart Agency</a></p>
    </footer>

</div><!--/.fluid-container-->
