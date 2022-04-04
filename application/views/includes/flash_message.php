<?php
	if ($this->session->flashdata('success_message')) {
		echo '<div class="alert alert-success">' . $this->session->flashdata('success_message') . '</div>';
	}
?>
<?php
	if ($this->session->flashdata('error_message')) {
		echo '<div class="alert alert-danger">' . $this->session->flashdata('error_message') . '</div>';
	}
?>
<?php
	if ($this->session->flashdata('info_message')) {
		echo '<div class="alert alert-info">' . $this->session->flashdata('info_message') . '</div>';
	}
?>