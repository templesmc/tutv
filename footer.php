
    </div><!-- #main -->
    
    <?php
    
    // action hook for placing content above the footer
    thematic_abovefooter();
    
    ?>    

	<div id="footer" class="container_12">
    
        <?php
        
        // action hook creating the footer 
        thematic_footer();
        
        ?>
        
	</div><!-- #footer -->
	
    <?php
    
    // actio hook for placing content below the footer
    thematic_belowfooter();
    
    ?>  

</div><!-- #wrapper .hfeed -->

<?php 

// calling WordPress' footer action hook
wp_footer();

// action hook for placing content before closing the BODY tag
thematic_after(); 

?>

</body>
</html>
