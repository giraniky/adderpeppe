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

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    <div class="float-right d-none d-sm-inline">
		Per qualsiasi esigenza non esitare a contattarmi <i class="fa fa-heart"></i>
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; <a href="https://t.me/Giuseppe" target="_blank">@Giuseppe</a>.</strong> All rights reserved.
  </footer>
</div>
<!-- ./wrapper -->


<!-- jQuery -->
<script src="../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>

<script>
let nome_pagina = $(".sidebar > nav > ul > li > a[href='<?php echo pathinfo($_SERVER["SCRIPT_FILENAME"], PATHINFO_BASENAME);?>'] > p").html();

$("title").html("Adder | " + nome_pagina);
$("h1.m-0").html(nome_pagina);
$(".breadcrumb-item.active").html(nome_pagina);

if(nome_pagina==="Home") {
  $(".breadcrumb-item")[1].remove();
  $(".breadcrumb-item")[0].remove();
}
else
  $(".breadcrumb-item")[1].innerHTML = $(".sidebar > nav > ul > li > a[href='<?php echo pathinfo($_SERVER["SCRIPT_FILENAME"], PATHINFO_BASENAME);?>'] > p").parent().parent().prevAll(".nav-header").html()


</script>

</body>
</html>