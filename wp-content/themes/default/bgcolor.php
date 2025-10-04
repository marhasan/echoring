<?require("header.php"); ?>

<div class="container-fluid">
	<div class="row flex-xl-nowrap">
		<main class="col py-md-3 pl-md-5 pr-md-5 bd-content" role="main">
			<div class="d-md-flex flex-md-row-reverse align-items-center justify-content-between">
				<a class="btn btn-sm btn-bd-light my-2 my-md-0" href="site.php?cmdSub=Listed" title="View Suspended" rel="noopener">View Sites</a>
				<h1 class="bd-title" id="content">
					Reset Updates
				</h1>
			</div>
			<p class="bd-lead">Here you can reset updates in order to write news.</p>
				<?
					$cmdSub = $_GET["cmdSub"];
					if($cmdSub=="")
					{
				?>
				<div class="bd-example">
					Are you sure you want to reset the updates?
				</div>
				<div class="highlight">
					<FORM NAME="frmbgcolor">
						<INPUT class="btn btn-outline-primary" TYPE="SUBMIT" NAME="cmdSub" VALUE="Hell Yea :-)">
						<INPUT class="btn btn-outline-primary" TYPE="SUBMIT" NAME="cmdSub" VALUE="No - please, I beg ya!">
					</FORM>
				</div>
				<?
				  }
				  else
				  {
				  if($cmdSub=="No - please, I beg ya!") { die("<div class='bd-example'>Connecting to database ... Connection established.</div><div class='highlight'><u>Running batch script:</u><BR>Delete primary keys ... Done<BR>Delete table content ... Error (could not delete row 35 in table MEMBER)<BR><BR>Delete table MEMBER ... Done<BR>Delete table UPDATER ... Done<BR>Delete table TOADD ... Done<BR>Delete table TOUPDATE ... Done<BR>Delete table NEWS ... Done<BR>Delete table OW ... Done<BR>Delete table PUB ... Done<BR><BR>Batch script finished - 1 error(s).<BR><BR><BR><BR><BR><BR><BR>Just kiddin, nothing happened :-)</div>"); }
				else
				$query = "UPDATE ar_member SET diff_bg = 0";
				$result = mysqli_query($dbc, $query)
				or die("Query failed");
				printf("<div class='alert alert-success' role='alert'><h4 class='alert-heading'>Well done!</h4><p>Cells background color have been reset</p><hr><p class='mb-0'>(%d\n sites have been changed)</p></div>", $dbc->affected_rows);
				}
				?>
			</main>
		</div>
	</div>
</body>
</html>