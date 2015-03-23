		</div>

		<div class="footer">
			<p class="textCenter">
				&copy; <?php echo date('Y'); ?> <a href="http://rentmediterraneanapartments.com" target="_blank">My-Home Property Management</a>
					<span><i class="fa fa-plus"></i></span>
					Provided by <a href="http://bitcoin.bigmoney.biz" target="_blank">Mars Systems International</a>
			</p>
		</div>

	</div>

	<script type="text/javascript" src="../js/jquery.js"></script>
	<script type="text/javascript" src="../js/bootstrap.min.js"></script>
	<script type="text/javascript" src="../js/reside.js"></script>
	<?php if (isset($stacktable)) { echo '<script type="text/javascript" src="../js/stacktable.js"></script>'; } ?>
	<?php if (isset($datePicker)) { echo '<script type="text/javascript" src="../js/datePicker.js"></script>'; } ?>
	<?php if (isset($jsFile)) { echo '<script type="text/javascript" src="validations/'.$jsFile.'.js"></script>'; } ?>

</body>
</html>