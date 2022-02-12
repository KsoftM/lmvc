<extend name="layout.app">
    <section name="title">Register Page</section>
    <section name="content">
        <div class="center">
            <form action="" method="post">
                <h1>Register</h1>
                <br>
                <?php if (\ksoftm\system\utils\Session::new()->haveKey('message')) : ?>
                    <?php echo ksoftm\system\utils\Session::new()->getOnceByKey('message') ?>
                    <br>
                    <br>
                <?php endif; ?>
                <input type="hidden" name="form_token" value="{{ var::token }}">
                <input type="text" value="{{ var::r_fn }}" name="firstName" placeholder="First Name"><br>
                <input type="text" value="{{ var::r_ln }}" name="lastName" placeholder="Last Name"><br>
                <input type="text" value="{{ var::r_un }}" name="username" placeholder="Username"><br>
                <input type="password" value="{{ var::r_ps }}" name="password" placeholder="Password"><br>
                <input type="email" value="{{ var::r_em }}" name="email" placeholder="Email"><br>
                <input type="submit" value="Register" name="register"><br>
            </form>
        </div>
    </section>
</extend>