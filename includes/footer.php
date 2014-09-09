		</div>

		<div class="footer">
			<p class="textCenter">
				&copy; <?php echo date('Y'); ?> <a href="http://www.gmsoft.com.ar">gmSoft</a>
				<span><i class="fa fa-plus"></i></span>
				
			</p>
		</div>

	</div>

	<script src="js/jquery.js"></script>
	<script src="js/bootstrap.js"></script>
	<script src="js/reside.js"></script>
	<?php if (isset($stacktable)) { echo '<script type="text/javascript" src="js/stacktable.js"></script>'; } ?>
	<?php if (isset($jsFile)) { echo '<script type="text/javascript" src="validations/'.$jsFile.'.js"></script>'; } ?>

</body>
</html>
