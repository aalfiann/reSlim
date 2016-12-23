<div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-plain">
                            <div class="header">

                            </div>
                            <hr>
                        </div>
                    </div>
                   
                    
<?php 

if(isset($_GET['nodrs'])) 
{
    include 'assets/lib/custom.php';
    if (CheckCabangID(strtoupper($_GET['nodrs']),$datalogin['cabangid']) == 1)
    {
        $urlself = $_SERVER['PHP_SELF']."?mode=detaildrs&nodrs=".$_GET['nodrs']."&filter=".((empty($_GET['filter']))?'':$_GET['filter'])."";
        $url = $api."?mode=detaildrs&nodrs=".$_GET['nodrs']."&filter=".((empty($_GET['filter']))?'':$_GET['filter'])."";	
	    $data = json_decode(curl_get_contents($url));
    
        //Get data hubkel
        $hubkel = $api."?mode=internalhubkel";
        $datahubkel = json_decode(curl_get_contents($hubkel));
        if (!empty($datahubkel))
        {
            if ($datahubkel->{'status'} == "success")
            {   
                $getdatahubkel = "";
                foreach ($datahubkel->results as $name => $hubkelvalue) 
	            {
                    $getdatahubkel .= '<option value="'.$hubkelvalue->{'Keterangan'}.'">'.$hubkelvalue->{'Keterangan'}.'</option>';            
                }
            }
            else
            {
                $getdatahubkel = '<option value="">Gagal mengambil data, silahkan reload halaman ini.</option>';
            }
        }

        //Get data dex
        $dex = $api."?mode=internaldex";
        $datadex = json_decode(curl_get_contents($dex));
        if (!empty($datadex))
        {
            if ($datadex->{'status'} == "success")
            {   
                $getdatadex = "";
                foreach ($datadex->results as $name => $dexvalue) 
    	        {
                    $getdatadex .= '<option value="'.$dexvalue->{'DevDexID'}.'">'.$dexvalue->{'Keterangan'}.'</option>';            
                }
            }
            else
            {
                $getdatadex = '<option value="">Gagal mengambil data, silahkan reload halaman ini.</option>';
            }
        }

    	if (!empty($data))
        {
            if ($data->{'status'} == "success")
            {
                //test get data request
                if(isset($_POST['token']))
                {
                    if (AlphaDecode($_POST['token'])==$_POST['Connote'])
                    {
                        //Eksekusi submit Delivered
                        if (!empty($_POST['submitdelivery'.$_POST['Connote']]))
                        {
                            if (UpdateStatusPODSukses(strtoupper($_POST['nodrs']),$_POST['Connote'],$_POST['koordinat'],strtoupper($_POST['penerima']).' ('.$_POST['jenishubkel'].')',$datalogin['userid']))
                            {
                                echo '<div class="col-md-12">
                                        <div class="alert alert-success">
                                            <span>Update Status POD<b> Success.</b></span>
                                        </div>
                                    </div>';
                                echo GoToPageMeta($urlself,1);
                            }
                            else
                            {
                                echo '<div class="col-md-12">
                                        <div class="alert alert-danger">
                                            <span>Update Status POD<b> Gagal.</b></span>
                                        </div>
                                    </div>';
                            }
                        }
                        //Eksekusi submit Dex
                        else if (!empty($_POST['submitdex'.$_POST['Connote']]))
                        {
                            if (UpdateStatusPODDEX(strtoupper($_POST['nodrs']),$_POST['Connote'],$_POST['jenisdex'],strtoupper($_POST['detaildex']),$datalogin['userid']))
                            {
                                echo '<div class="col-md-12">
                                        <div class="alert alert-success">
                                            <span>Update Status POD<b> Success.</b></span>
                                        </div>
                                    </div>';
                                echo GoToPageMeta($urlself,1);
                            }
                            else
                            {
                                echo '<div class="col-md-12">
                                        <div class="alert alert-danger">
                                            <span>Update Status POD<b> Gagal.</b></span>
                                        </div>
                                    </div>';
                            }
                        }
                    }
                }

                echo '<div class="col-md-12">
                            <div class="card card-plain">
                                <div class="header">
                                    <h4 class="title text-uppercase">No DRS: '.$_GET['nodrs'].'</h4>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="pull-left">
                                                <form id="mystatus" method="get">
                                                    <input name="m" type="text" value="4" hidden>
                                                    <input name="mode" type="text" value="detaildrs" hidden>
                                                    <input name="nodrs" type="text" value="'.$_GET['nodrs'].'" hidden>
                                                    <select name = "filter" class="form-control border-input text-uppercase" style = "position: relative" onchange="change()">
                                                        <option value="ALL" '.(($_GET['filter']=='ALL'?'selected':'')).'>ALL</option>
                                                        <option value="" '.((empty($_GET['filter'])?'selected':'')).'>NONE</option>
                                                        <option value="DEX" '.(($_GET['filter']=='DEX'?'selected':'')).'>DEX</option>
                                                        <option value="DELIVERED" '.(($_GET['filter']=='DELIVERED'?'selected':'')).'>DELIVERED</option>
                                                    </select>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <script>function change(){document.getElementById("mystatus").submit();}</script>
                                <br>
                                <div class="content table-responsive table-full-width">
                                    <table class="table table-striped">
                                        <thead>
                                            <th>No</th>
                                        	<th>Tanggal Connote</th>
                                        	<th>Connote</th>
                                        	<th>Produk</th>
                                    	    <th>Kg</th>
                                            <th>Koli</th>
                                        	<th>Status</th>
                                        </thead>
                                        <tbody>
                                            <tr>';
                $n=1;
                foreach ($data->results as $name => $value) 
	            {
                    echo '<td >' . $n++ .'</td>';
                    echo '<td >' . $value->{'Tanggal Connote'} .'</td>';
		    	    echo '<td >'.(($value->{'Status'} == '')?'<a data-dismiss="modal" data-toggle="modal" href="#updatestatus' . $value->{'Connote'} .'" title="Update Status ' . $value->{'Connote'} .'" onclick="getLocation'.$value->{'Connote'}.'()">' . $value->{'Connote'} .'</a></td>':$value->{'Connote'} .'</td>');
    		    	echo '<td >' . $value->{'Produk'} .'</td>';
			        echo '<td >' . $value->{'Kg'} .'</td>';
        			echo '<td >' . $value->{'Koli'} .'</td>';
                	echo '<td >' . $value->{'Status'} .'</td>';
	        		echo '</tr>';              
                }
                echo '</table>
                </div>
                </div>
            </div>';
                foreach ($data->results as $name => $value) 
    	        {
                    if ($value->{'Status'}=='')
                    {
                        echo '<div class="modal fade" id="updatestatus' . $value->{'Connote'} .'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel">Input Status POD</h4>
                                  </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <!-- Nav tabs -->
                                            <ul class="nav nav-tabs" role="tablist">
                                                <li role="presentation" class="active"><a href="#delivered' . $value->{'Connote'} .'" aria-controls="delivered" role="tab" data-toggle="tab">DELIVERED</a></li>
                                                <li role="presentation"><a href="#dex' . $value->{'Connote'} .'" aria-controls="dex" role="tab" data-toggle="tab">DEX</a></li>
                                            </ul>
                                            <!-- Tab panes -->
                                            <div class="tab-content">
                                                <!-- TAB DELIVERED -->
                                                <div role="tabpanel" class="tab-pane active" id="delivered' . $value->{'Connote'} .'">
                                                    <br>
                                                    <form method="post" action="'.$urlself.'" enctype="multipart/form-data">
                                                    <div class="form-group">
                                                        <input name="m" type="text" value="4" hidden>
                                                        <input name="mode" type="text" value="detaildrs" hidden>
                                                        <input name="nodrs" type="text" value="'.$_GET['nodrs'].'" hidden>
                                                        <input name="filter" type="text" value="'.$_GET['filter'].'" hidden>
                                                        <input name="token" type="text" value="'.AlphaEncode($value->{'Connote'}).'" hidden>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>No Connote</label>
                                                        <input name="Connote" type="text" class="form-control border-input text-uppercase" placeholder="No Connote Anda" value="' . $value->{'Connote'} .'" readonly>
                                                    </div>
                                                    <div class="col-md-8">
                                                    <div class="form-group">
                                                        <input id="koordinat'.$value->{'Connote'}.'" name="koordinat" type="text" class="form-control border-input text-uppercase" value="0, 0" readonly>
                                                    </div>
                                                    </div>
                                                    <div class="form-group text-center">
                                                        <button type="button" class="btn btn-info btn-fill btn-wd" onclick="getLocation'.$value->{'Connote'}.'()">Refresh Lokasi</button>
                                                    </div>
                                                    <hr>
                                                    <div class="form-group">
                                                        <label>Diterima Oleh</label>
                                                        <input name="penerima" type="text" class="form-control border-input text-uppercase" placeholder="Diterima oleh ..." value="" maxlength="20" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Hubungan Status Penerima</label>
                                                        <select name="jenishubkel"  style="max-height:200px; overflow-y:scroll; overflow-x:hidden;" class="form-control border-input" required>
                                                            '.$getdatahubkel.'
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Upload Foto POD</label>
                                                        <input type="file" name="datafile" accept="image/*" capture="camera" class="form-control border-input" required></input>
                                                    </div>
                                                    <div class="form-group text-center">
                                                        <input name="submitdelivery' . $value->{'Connote'} .'" type="submit" class="btn btn-primary" value="Submit DELIVERED"></input>
                                                    </div>
                                                    </form>
                                                    <script>var x'.$value->{'Connote'}.'=document.getElementById("koordinat'.$value->{'Connote'}.'");function getLocation'.$value->{'Connote'}.'(){if (navigator.geolocation) {navigator.geolocation.getCurrentPosition(showPosition'.$value->{'Connote'}.');} else {x'.$value->{'Connote'}.'.value = "0, 0";}}function showPosition'.$value->{'Connote'}.'(position) {x'.$value->{'Connote'}.'.value = position.coords.latitude + ", " + position.coords.longitude;}</script>
                                                </div>
                                                <!-- TAB DEX -->
                                                <div role="tabpanel" class="tab-pane" id="dex' . $value->{'Connote'} .'">
                                                    <br>
                                                    <form method="post" action="'.$urlself.'">
                                                    <div class="form-group">
                                                        <input name="m" type="text" value="4" hidden>
                                                        <input name="mode" type="text" value="detaildrs" hidden>
                                                        <input name="nodrs" type="text" value="'.$_GET['nodrs'].'" hidden>
                                                        <input name="filter" type="text" value="'.$_GET['filter'].'" hidden>
                                                        <input name="token" type="text" value="'.AlphaEncode($value->{'Connote'}).'" hidden>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>No Connote</label>
                                                        <input name="Connote" type="text" class="form-control border-input text-uppercase" placeholder="No Connote Anda" value="' . $value->{'Connote'} .'" readonly>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Jenis DEX</label>
                                                        <select name="jenisdex"  style="max-height:200px; overflow-y:scroll; overflow-x:hidden;" class="form-control border-input" required>
                                                            '.$getdatadex.'
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Detail DEX</label>
                                                        <textarea rows="5" name="detaildex" class="form-control border-input text-uppercase" placeholder="Alasan detail DEX..." value="" maxlength="200"></textarea>
                                                    </div>
                                                    <div class="form-group text-center">
                                                        <input name="submitdex' . $value->{'Connote'} .'" type="submit" class="btn btn-primary" value="Submit DEX"></input>
                                                    </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>';
                    }
                }
            }
            else
            {
                echo '<div class="col-md-12">
                            <div class="card card-plain">
                                <div class="header">
                                    <h4 class="title text-uppercase">No DRS: '.$_GET['nodrs'].'</h4>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="pull-left">
                                                <form id="mystatus" method="get">
                                                    <input name="m" type="text" value="4" hidden>
                                                    <input name="mode" type="text" value="detaildrs" hidden>
                                                    <input name="nodrs" type="text" value="'.$_GET['nodrs'].'" hidden>
                                                    <select name = "filter" class="form-control border-input text-uppercase" style = "position: relative" onchange="change()">
                                                        <option value="ALL" '.(($_GET['filter']=='ALL'?'selected':'')).'>ALL</option>
                                                        <option value="" '.(($_GET['filter']==''?'selected':'')).'>NONE</option>
                                                        <option value="DEX" '.(($_GET['filter']=='DEX'?'selected':'')).'>DEX</option>
                                                        <option value="DELIVERED" '.(($_GET['filter']=='DELIVERED'?'selected':'')).'>DELIVERED</option>
                                                    </select>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <p class="category">Message: '.$data->{'message'}.'</p>
                                </div>
                                <script>function change(){document.getElementById("mystatus").submit();}</script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
            } 
        }
    }
    else
    {
        echo '<div class="col-md-12">
                        <div class="card card-plain">
                            <div class="header">
                                <h4 class="title">Maaf Anda tidak memiliki wewenang untuk melihat DRS cabang lain</h4>
                            </div>
                        </div>
                    </div>';
    }
}
                    ?>
                            </div>
                        </div>
                    </div>

                