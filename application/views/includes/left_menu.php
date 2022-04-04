	<div class="page-sidebar-wrapper">
		<!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
		<!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
		<div class="page-sidebar md-shadow-z-2-i  navbar-collapse collapse">
			<!-- BEGIN SIDEBAR MENU -->
			<!-- DOC: Apply "page-sidebar-menu-light" class right after "page-sidebar-menu" to enable light sidebar menu style(without borders) -->
			<!-- DOC: Apply "page-sidebar-menu-hover-submenu" class right after "page-sidebar-menu" to enable hoverable(hover vs accordion) sub menu mode -->
			<!-- DOC: Apply "page-sidebar-menu-closed" class right after "page-sidebar-menu" to collapse("page-sidebar-closed" class must be applied to the body element) the sidebar sub menu mode -->
			<!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
			<!-- DOC: Set data-keep-expand="true" to keep the submenues expanded -->
			<!-- DOC: Set data-auto-speed="200" to adjust the sub menu slide up/down speed -->
			<ul class="page-sidebar-menu " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
				<li class="start <?php echo ($left_menu == 'Dashboard')?'active':''; ?>">
					<a href="<?php echo site_url('admin'); ?>">
					<i class="icon-home"></i>
					<span class="title">Dashboard</span>
					</a>
				</li>
                <li class="<?php echo ($left_menu == 'Courses_module')?'active open':''; ?>">
					<a href="javascript:;">
					<i class="fa fa-th" aria-hidden="true"></i>
					<span class="title">Courses module</span>
					<span class="arrow <?php echo ($left_menu == 'Courses_module')?'open':''; ?>"></span>
					</a>
					<ul class="sub-menu">
						<li class="<?php echo (isset($left_submenu) && $left_submenu == 'Courses_category')?'active':''; ?>">
							<a href="<?php echo base_url('admin/courses_category'); ?>">		
							<i class="fa fa-rss" aria-hidden="true"></i>
							<span class="title">Course category</span>	
							</a>
						</li>
						<li class="<?php echo (isset($left_submenu) && $left_submenu == 'Courses')?'active':''; ?>">
							<a href="<?php echo base_url('admin/courses'); ?>">		
							<i class="fa fa-rss-square" aria-hidden="true"></i>
							<span class="title">Courses</span>	
							</a>
						</li>
                        <li class="<?php echo (isset($left_submenu) && $left_submenu == 'Courses_comments')?'active':''; ?>">
							<a href="<?php echo base_url('admin/courses_comments'); ?>">		
							<i class="fa fa-commenting-o" aria-hidden="true"></i>
							<span class="title">Course comments</span>	
							</a>
						</li>	
					</ul>
				</li> 

				<li class="<?php echo ($left_menu == 'Services_module')?'active open':''; ?>">
					<a href="javascript:;">
					<i class="fa fa-asterisk" aria-hidden="true"></i>
					<span class="title">Services module</span>
					<span class="arrow <?php echo ($left_menu == 'Services_module')?'open':''; ?>"></span>
					</a>
					<ul class="sub-menu">
						<li class="<?php echo (isset($left_submenu) && $left_submenu == 'Services_category')?'active':''; ?>">
							<a href="<?php echo base_url('admin/services_category'); ?>">		
							<i class="fa fa-adjust" aria-hidden="true"></i>
							<span class="title">Service category</span>	
							</a>
						</li>
						<li class="<?php echo (isset($left_submenu) && $left_submenu == 'Services')?'active':''; ?>">
							<a href="<?php echo base_url('admin/services'); ?>">		
							<i class="fa fa-arrows" aria-hidden="true"></i>
							<span class="title">Services</span>	
							</a>
						</li>	
					</ul>
				</li> 

				<li class="<?php echo ($left_menu == 'Assignment_module')?'active open':''; ?>">
					<a href="javascript:;">
					<i class="fa fa-book" aria-hidden="true"></i>
					<span class="title">Assignment module</span>
					<span class="arrow <?php echo ($left_menu == 'Assignment_module')?'open':''; ?>"></span>
					</a>
					<ul class="sub-menu">
						<li class="<?php echo (isset($left_submenu) && $left_submenu == 'Assignment')?'active':''; ?>">
							<a href="<?php echo base_url('admin/assignment'); ?>">		
							<i class="fa fa-bookmark" aria-hidden="true"></i>
							<span class="title">Assignment</span>	
							</a>
						</li>	
					</ul>
				</li>		
					
				<li class="<?php echo ($left_menu == 'Site_options')?'active open':''; ?>">
					<a href="javascript:;">
					<i class="fa fa-pencil-square-o"></i>
					<span class="title">Site options module</span>
					<span class="arrow <?php echo ($left_menu == 'Site_options')?'open':''; ?>"></span>
					</a>
					<ul class="sub-menu">
						<li class="<?php echo (isset($left_submenu) && $left_submenu == 'Website_setting')?'active':''; ?>">
							<a href="<?php echo base_url('admin/website'); ?>">		
							<i class="icon-settings"></i>
							<span class="title">Website setting</span>	
							</a>
						</li>	
						<li class="<?php echo (isset($left_submenu) && $left_submenu == 'Static_Pages')?'active':''; ?>">
							<a href="<?php echo site_url('admin/static-page'); ?>">
							<i class="fa fa-file" aria-hidden="true"></i>
							<span class="title">Static pages</span>	
							</a>
						</li>
						<li class="<?php echo (isset($left_submenu) && $left_submenu == 'Static_content')?'active':''; ?>">
							<a href="<?php echo site_url('admin/static_content'); ?>">
							<i class="fa fa-paragraph" aria-hidden="true"></i>
							<span class="title">Static content</span>	
							</a>
						</li>
						<li class="<?php echo (isset($left_submenu) && $left_submenu == 'Mail_templates')?'active':''; ?>">
							<a href="<?php echo site_url('admin/mail_templates'); ?>">
							<i class="fa fa-envelope" aria-hidden="true"></i>
							<span class="title">Email templates</span>	
							</a>
						</li>
						<li class="<?php echo (isset($left_submenu) && $left_submenu == 'Contact')?'active':''; ?>">
							<a href="<?php echo site_url('admin/contact_queries'); ?>">
							<i class="fa fa-phone-square" aria-hidden="true"></i>
							<span class="title">Contact Queries</span>
							</a>
						</li>
						<li class="<?php echo (isset($left_submenu) && $left_submenu == 'Home_slider')?'active':''; ?>">
							<a href="<?php echo site_url('admin/home_slider'); ?>">
							<i class="fa fa-paragraph" aria-hidden="true"></i>
							<span class="title">Home slider</span>	
							</a>
						</li>
                        <li class="<?php echo (isset($left_submenu) && $left_submenu == 'Newsletter')?'active':''; ?>">
							<a href="<?php echo site_url('admin/newsletter'); ?>">
							<i class="fa fa-newspaper-o" aria-hidden="true"></i>
							<span class="title">Newsletter</span>
							</a>
						</li>					
					</ul>
				</li>  
			</ul>
			<!-- END SIDEBAR MENU -->
		</div>
	</div>
	<!-- END SIDEBAR