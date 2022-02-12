<extend name="layout.app">
    <section name="title">Login Page</section>
    <section name="content">
        <div class="center">
            <form action="" method="post">
                <h1>
                    <lang src="login" />
                </h1>
                <br>
                <?php if (\ksoftm\system\utils\Session::new()->haveKey('message')) : ?>
                    <?php echo ksoftm\system\utils\Session::new()->getOnceByKey('message') ?>
                    <br>
                    <br>
                <?php endif; ?>
                <input type="hidden" name="form_token" value="{{ var::token }}">
                <input type="text" name="username" placeholder="Username"><br>
                <input type="password" name="password" placeholder="Password"><br>
                <input type="submit" value="Login" name="login"><br>
            </form>
        </div>
    </section>
</extend>