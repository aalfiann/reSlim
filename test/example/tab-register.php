            <div class="content">
                <div class="container">
                    <div class="row">
                        <div class="col-md-4 col-sm-6 col-md-offset-3 col-sm-offset-4">
                        <?php
                            if (isset($_POST['submitregister']))
                            {
                                if ($_POST['password1'] == $_POST['password2'])
                                {
                                    DoRegister(strtoupper($_POST['kuririd']),strtoupper($_POST['username']),$_POST['password2']);
                                }
                                else
                                {
                                    echo '<div class="alert alert-danger" role="alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <strong>Proses Register Gagal,</strong> Password yang Anda masukkan tidak cocok! 
                                        </div>';
                                }
                            } 
                        ?>
                            <form action="<?php $_SERVER['PHP_SELF']?>" method="post">
                                <div class="card" data-background="color" data-color="blue">
                                    <div class="header">
                                        <h3 class="title">Form Register</h3>
                                        <hr>
                                    </div>
                                    <div class="content">
                                        <div class="form-group">
                                            <label>ID Kurir</label>
                                            <input name="kuririd" type="text" placeholder="ID Kurir Anda" class="form-control border-input text-uppercase" maxlength="11" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Username</label>
                                            <input name="username" type="username" placeholder="Username MIND Anda" class="form-control border-input text-uppercase" maxlength="20" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Password</label>
                                            <input name="password1" type="password" placeholder="Password Anda" class="form-control border-input" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Ulangi Password</label>
                                            <input name="password2" type="password" placeholder="Ulangi Password Anda" class="form-control border-input" required>
                                        </div>
                                        <hr>
                                        <div class="form-group text-center">
                                            <button name="submitregister" type="submit" class="btn btn-fill btn-wd ">Register</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>