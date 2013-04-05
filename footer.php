        </div><!-- #container -->

        <?php thematic_belowcontainer(); ?>

    </div><!-- #main -->
    
    <?php
    
    // action hook for placing content above the footer
    thematic_abovefooter();
    
    ?>    

    </div><!-- #wrapper .hfeed -->

	<div id="footer" class="clearfix">

        <div id="footer-inner" class="container_12 clearfix">
    
            <?php
            
            // action hook creating the footer 
            thematic_footer();
            
            ?>

        </div> <!-- end #footer-inner -->
        
	</div><!-- #footer -->
	
    <?php
    
    // actio hook for placing content below the footer
    thematic_belowfooter();
    
    ?>  

<?php 

// calling WordPress' footer action hook
wp_footer();

// action hook for placing content before closing the BODY tag
thematic_after(); 

?>

</body>
</html>
