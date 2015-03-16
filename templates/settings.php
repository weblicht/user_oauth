<form id="user_oauth" action="#" method='post'>
    <div class="section">
        <h2>OAuth</h2>
        <label for="introspectionEndpoint ">Introspection point:</label><input type="text" size="80" name="introspectionEndpoint" id="introspectionEndpoint" value="<?php p($_['introspectionEndpoint']); ?>" title="<?php p($l->t('Introspection endpoint'));?>" />
        <br/>
        <span class="msg"><?php p($l->t('Provide the OAuth 2.0 Authorization Server introspection endpoint here.'));?></span>
        <br/>
        <input type="submit" value="Save" />
    </div>
</form>
