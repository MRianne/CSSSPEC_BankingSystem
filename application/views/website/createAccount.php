
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0 text-dark"></h1>
				</div><!-- /.col -->

			</div><!-- /.row -->
		</div><!-- /.container-fluid -->
	</div>
	<!-- /.content-header -->

	<!-- Main content -->
	<div class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-lg-12">
					<div class="card">
						<div class="card-body ">

							<h5 class="card-title">Open Account</h5>
							<hr>
							<div class="form-group">
								<?php echo validation_errors(); ?>
								<?php
								if ($this->session->flashdata('error_message') !== null) {
									echo "<span style=\"color: red;text-align: center; font-weight: bold\" id = \"result\" name = \"result\">
									<div>
									<p>".$this->session->flashdata('error_message')."</p>
									</div>
									</span>";
								}
								?>
								<?= $this->session->flashdata("message") ?? null ?>

								<?php echo form_open('account/create/' . $id); ?>
								<!-- <label>Account Number</label>
								<input class="form-control" type="text" name="account_id" value=<?= $account_id ?> readonly="true" /> -->
								<label>Account Type</label>
								<select class="form-control" name="type_id">
									<?php foreach($types as $type): ?>
									<option value="<?= $type['type_id'] ?>"><?= '"' . $type['description'] . '" Required Initial Deposit: ' . number_format($type['initial_deposit'],2)  ?></option>
									<?php endforeach; ?>
								</select>
								<br>
								<div><input class="btn btn-primary" type="submit" value="Submit" /></div>
								</form>
							</div>
						</div>
					</div>

				</div>

			</div>
			<!-- /.row -->
		</div><!-- /.container-fluid -->
	</div>
	<!-- /.content -->
</div>
<!-- /.content-wrapper -->



<!-- Main Footer -->
<footer class="main-footer">
	<!-- To the right -->
	<div class="float-right d-none d-sm-inline">
		Lodi Group
	</div>
	<!-- Default to the left -->
	<strong>SQ is life. </strong> All rights reserved.
</footer>
</div>
<!-- ./wrapper -->

