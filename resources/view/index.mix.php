<extend name="layout.app">
    <section name="title">Index Page</section>
    <section name="content">
        <h2>
            <lang src="welcome" />
        </h2>
        <p>
            Download page still alive.
            <br>
        <p>
            <var name="url" />
            <!--/p-->
        </p>
        {{ var::url }}

        <a href="{{ var::url }}">DownloadPath</a>
        <br>
        <br>
        <form action="{{var::lang}}" method="post">
            <select name="lang">
                <option value="en">English</option>
                <option value="ta">Tamil</option>
            </select>

            <input type="submit" value="Change Language">
        </form>
    </section>
</extend>