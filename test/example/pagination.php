<?php

    class Pagination {
        public function makePagination($data,$links){
            echo '<ul class="pagination">'; // Start Pagination
                $itemsperpage = $data->metadata->{'items_per_page'};
                $pagenow = $data->metadata->{'page_now'};
                $pagetotal = $data->metadata->{'page_total'};

			    if ($pagenow <= $pagetotal)
                {
                    //Middle Pagination = If this page + 2 < total page
                    if (($pagenow + 2) < $pagetotal && $pagenow >= 3)
                    {
                        echo '<li><a href="'.$links.'&page='.($pagenow-1).'&itemsperpage='.$itemsperpage.'"><i class="fa fa-angle-double-left" aria-hidden="true"></i></a></li>';
                        for ($p=($pagenow-2);$p<=($pagenow+2);$p++)
                        {
                            echo '<li '.(($p == $pagenow)?'class="active"':'').'><a href="'.$links.'&page='.$p.'&itemsperpage='.$itemsperpage.'">'.$p.'</a></li>';
                        }
                        echo '<li><a href="'.$links.'&page='.($pagenow+1).'&itemsperpage='.$itemsperpage.'"><i class="fa fa-angle-double-right" aria-hidden="true"></i></a></li>';
                    }
                    //Last Pagination = total page >= 5 and if this page + 2 >= total page
                    elseif (($pagenow + 2) >= $pagetotal && $pagetotal >= 5)
                    {
                        echo ((($pagenow-1)>0)?'<li><a href="'.$links.'&page='.($pagenow-1).'&itemsperpage='.$itemsperpage.'"><i class="fa fa-angle-double-left" aria-hidden="true"></i></a></li>':'');
                        for ($p=($pagetotal-4);$p<=$pagetotal;$p++)
                        {
                            echo '<li '.(($p == $pagenow)?'class="active"':'').'><a href="'.$links.'&page='.$p.'&itemsperpage='.$itemsperpage.'">'.$p.'</a></li>';
                        }
                        echo (($pagenow<$pagetotal)?'<li><a href="'.$links.'&page='.($pagenow+1).'&itemsperpage='.$itemsperpage.'"><i class="fa fa-angle-double-right" aria-hidden="true"></i></a></li>':'');
                    }
                    //Last Pagination = total page < 5 and if this page + 2 >= total page
                    elseif (($pagenow + 2) >= $pagetotal && $pagetotal < 5)
                    {
                        echo ((($pagenow-1)>0)?'<li><a href="'.$links.'&page='.($pagenow-1).'&itemsperpage='.$itemsperpage.'"><i class="fa fa-angle-double-left" aria-hidden="true"></i></a></li>':'');
                        for ($p=($pagetotal-($pagetotal-1));$p<=$pagetotal;$p++)
                        {
                            echo '<li '.(($p == $pagenow)?'class="active"':'').'><a href="'.$links.'&page='.$p.'&itemsperpage='.$itemsperpage.'">'.$p.'</a></li>';
                        }
                        echo (($pagenow<$pagetotal)?'<li><a href="'.$links.'&page='.($pagenow+1).'&itemsperpage='.$itemsperpage.'"><i class="fa fa-angle-double-right" aria-hidden="true"></i></a></li>':'');
                    }
                    //First pagination and if total page <= 5
                    elseif ($pagetotal <= 5) 
                    {
                        echo ((($pagenow-1)>0)?'<li><a href="'.$links.'&page='.($pagenow-1).'&itemsperpage='.$itemsperpage.'"><i class="fa fa-angle-double-left" aria-hidden="true"></i></a></li>':'');
                        for ($p=1;$p<=$pagetotal;$p++)
                        {
                            echo '<li '.(($p == $pagenow)?'class="active"':'').'><a href="'.$links.'&page='.$p.'&itemsperpage='.$itemsperpage.'">'.$p.'</a></li>';
                        }
                        echo '<li><a href="'.$links.'&page='.($pagenow+1).'&itemsperpage='.$itemsperpage.'"><i class="fa fa-angle-double-right" aria-hidden="true"></i></a></li>';
                    }
                    //First pagination and if total page > 5
                    elseif ($pagetotal > 5 && $pagenow <=2) 
                    {
                        echo ((($pagenow-1)>0)?'<li><a href="'.$links.'&page='.($pagenow-1).'&itemsperpage='.$itemsperpage.'"><i class="fa fa-angle-double-left" aria-hidden="true"></i></a></li>':'');
                        for ($p=1;$p<=5;$p++)
                        {
                            echo '<li '.(($p == $pagenow)?'class="active"':'').'><a href="'.$links.'&page='.$p.'&itemsperpage='.$itemsperpage.'">'.$p.'</a></li>';
                        }
                        echo '<li><a href="'.$links.'&page='.($pagenow+1).'&itemsperpage='.$itemsperpage.'"><i class="fa fa-angle-double-right" aria-hidden="true"></i></a></li>';
                    }
                }	
	    		echo '</ul> '; // End Pagination
        }
    }