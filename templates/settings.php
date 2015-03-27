<form id="user_oauth" action="#" method='post'>
    <div class="section">
        <h2>OAuth</h2>
        <span class="msg"><?php p($l->t('Provide the OAuth 2.0 Authorization Server introspection endpoint here. If authentication is required, specify the user name and password as well.'));?></span>
        <br/>
        <label for="introspectionEndpoint ">Introspection point:</label><input type="text" size="100" name="introspectionEndpoint" id="introspectionEndpoint" value="<?php p($_['introspectionEndpoint']); ?>" title="<?php p($l->t('Introspection endpoint'));?>" />
        <br/>
        <label for="username">User name:</label><input type="text" size="100" name="username" id="username" value="<?php p($_['username']); ?>" title="<?php p($l->t('User name:'));?>" />
        <br/>
        <label for="password">Password:</label><input type="password" size="100" name="password" id="password" value="<?php p($_['password']); ?>" title="<?php p($l->t('Password:'));?>" />
        <br/>
        <input type="submit" value="Save" />
    </div>
</form>
