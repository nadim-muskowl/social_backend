<div class="container">    
    <h1>Welcome <?=$name?></h1>

    <div class="list-group">
        <a href="#" class="list-group-item list-group-item-action">Admin List</a>        
        <a href="#" class="list-group-item list-group-item-action">User List</a>        
    </div>

    <p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds. 
        <?php echo (ENVIRONMENT === 'development') ? 'CodeIgniter Version <strong>' . CI_VERSION . '</strong>' : '' ?>
    </p>
</div>
