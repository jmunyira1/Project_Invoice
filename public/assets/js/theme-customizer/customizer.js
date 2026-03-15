(function ($) {
    if (localStorage.getItem("color")) {
        $("#color").attr("href", "../assets/css/" + localStorage.getItem("color") + ".css");
    }
    if (localStorage.getItem("dark")) {
        $("body").attr("class", localStorage.getItem("dark"));
    }

    // ── Build the single settings panel (no layouts, no links) ──────
    $(
        '<div class="sidebar-panel-main"><ul>' +
        '<li id="cog-click"><a href="javascript:void(0)" class="cog-click icon-btn btn-primary"><i class="fa-solid fa-gear fa-spin"></i></a><span>Settings</span></li>' +
        '</ul></div>' +
        '<section class="setting-sidebar">' +
        '<div class="customizer-header">' +
        '<div class="theme-title">' +
        '<div><h4>Preview Settings</h4><p class="mb-0">Try It Real Time</p></div>' +
        '<div class="flex-grow-1"><a class="icon-btn btn-outline-light button-effect pull-right cog-close" id="cog-close"><i class="fa-solid fa-xmark fa-fw"></i></a></div>' +
        '</div>' +
        '</div>' +
        '<div class="customizer-body custom-scrollbar">' +

        '<h6>Unlimited Color</h6>' +
        '<ul class="layout-grid unlimited-color-layout">' +
        '<input id="ColorPicker1" name="Background" type="color" value="#7366ff">' +
        '<input id="ColorPicker2" name="Background" type="color" value="#838383">' +
        '<button class="color-apply-btn btn btn-primary" type="button">Apply</button>' +
        '</ul>' +

        '<h6>Light Layout</h6>' +
        '<ul class="layout-grid customizer-color">' +
        '<li class="color-layout" data-attr="color-1" data-primary="#7366ff" data-secondary="#838383"><div></div></li>' +
        '<li class="color-layout" data-attr="color-2" data-primary="#4831D4" data-secondary="#ea2087"><div></div></li>' +
        '<li class="color-layout" data-attr="color-3" data-primary="#d64dcf" data-secondary="#8e24aa"><div></div></li>' +
        '<li class="color-layout" data-attr="color-4" data-primary="#4c2fbf" data-secondary="#2e9de4"><div></div></li>' +
        '<li class="color-layout" data-attr="color-5" data-primary="#7c4dff" data-secondary="#7b1fa2"><div></div></li>' +
        '<li class="color-layout" data-attr="color-6" data-primary="#3949ab" data-secondary="#4fc3f7"><div></div></li>' +
        '</ul>' +

        '<h6>Dark Layout</h6>' +
        '<ul class="layout-grid customizer-color dark">' +
        '<li class="color-layout" data-attr="color-1" data-primary="#4466f2" data-secondary="#1ea6ec"><div></div></li>' +
        '<li class="color-layout" data-attr="color-2" data-primary="#4831D4" data-secondary="#ea2087"><div></div></li>' +
        '<li class="color-layout" data-attr="color-3" data-primary="#d64dcf" data-secondary="#8e24aa"><div></div></li>' +
        '<li class="color-layout" data-attr="color-4" data-primary="#4c2fbf" data-secondary="#2e9de4"><div></div></li>' +
        '<li class="color-layout" data-attr="color-5" data-primary="#7c4dff" data-secondary="#7b1fa2"><div></div></li>' +
        '<li class="color-layout" data-attr="color-6" data-primary="#3949ab" data-secondary="#4fc3f7"><div></div></li>' +
        '</ul>' +

        '<h6>Sidebar Icon</h6>' +
        '<ul class="layout-grid sidebar-setting">' +
        '<li class="active" data-attr="stroke-svg"><div class="bg-light header"><ul><li></li><li></li><li></li></ul></div><div class="bg-light body"><span class="badge badge-primary">Stroke</span></div></li>' +
        '<li data-attr="fill-svg"><div class="bg-light header"><ul><li></li><li></li><li></li></ul></div><div class="bg-light body"><span class="badge badge-primary">Fill</span></div></li>' +
        '</ul>' +

        '<h6>Mix Layout</h6>' +
        '<ul class="layout-grid customizer-mix">' +
        '<li class="color-layout active" data-attr="light-only"><div class="bg-light header"><ul><li></li><li></li><li></li></ul></div><div class="body common-layout"><span class="badge badge-secondary">Light</span><ul><li class="bg-light sidebar"></li><li class="bg-light body"></li></ul></div></li>' +
        '<li class="color-layout" data-attr="dark-sidebar"><div class="bg-light header"><ul><li></li><li></li><li></li></ul></div><div class="body common-layout"><span class="badge badge-secondary">Sidebar</span><ul><li class="sidebar bg-dark"></li><li class="bg-light body"></li></ul></div></li>' +
        '<li class="color-layout" data-attr="dark-only"><div class="header bg-dark"><ul><li></li><li></li><li></li></ul></div><div class="body common-layout"><span class="badge badge-secondary">Dark</span><ul><li class="sidebar bg-dark"></li><li class="body bg-dark"></li></ul></div></li>' +
        '<li class="color-layout" id="auto-layout" onclick="detectColorScheme()"><div class="bg-light header"><ul><li></li><li></li><li></li></ul></div><div class="body common-layout"><span class="badge badge-secondary">Auto</span><ul><li class="sidebar bg-dark"></li><li class="bg-light body"></li></ul></div></li>' +
        '</ul>' +

        '</div>' +
        '</section>'
    ).appendTo($("body"));

    // ── Panel open / close ──────────────────────────────────────────

    $(document).ready(function () {

        document.getElementById("cog-click").addEventListener("click", function () {
            document.querySelector(".setting-sidebar").classList.add("open");
        });

        document.getElementById("cog-close").addEventListener("click", function () {
            document.querySelector(".setting-sidebar").classList.remove("open");
        });

        document.addEventListener("click", function (event) {
            if (!event.target.closest("#cog-click")) {
                document.querySelector(".setting-sidebar").classList.remove("open");
            }
        });

        // ── Colour scheme (light) ─────────────────────────────────────

        $(".customizer-color li").on("click", function () {
            $(".customizer-color li").removeClass("active");
            $(this).addClass("active");
            var color = $(this).attr("data-attr");
            var primary = $(this).attr("data-primary");
            var secondary = $(this).attr("data-secondary");
            localStorage.setItem("color", color);
            localStorage.setItem("primary", primary);
            localStorage.setItem("secondary", secondary);
            localStorage.removeItem("dark");
            $("#color").attr("href", "../assets/css/" + color + ".css");
            $(".dark-only").removeClass("dark-only");
            location.reload(true);
        });

        // ── Colour scheme (dark) ──────────────────────────────────────

        $(".customizer-color.dark li").on("click", function () {
            $(".customizer-color.dark li").removeClass("active");
            $(this).addClass("active");
            $("body").attr("class", "dark-only");
            localStorage.setItem("dark", "dark-only");
        });

        // ── Custom colour pickers ─────────────────────────────────────

        if (localStorage.getItem("primary") != null) {
            document.documentElement.style.setProperty("--theme-default", localStorage.getItem("primary"));
        }
        if (localStorage.getItem("secondary") != null) {
            document.documentElement.style.setProperty("--theme-secondary", localStorage.getItem("secondary"));
        }

        document.getElementById("ColorPicker1").onchange = function () {
            localStorage.setItem("primary", this.value);
            document.documentElement.style.setProperty("--theme-primary", this.value);
        };

        document.getElementById("ColorPicker2").onchange = function () {
            localStorage.setItem("secondary", this.value);
            document.documentElement.style.setProperty("--theme-secondary", this.value);
        };

        $(".color-apply-btn").click(function () {
            location.reload(true);
        });

        // ── Mix layout (light / dark sidebar / dark / auto) ───────────

        $(".customizer-mix li").on("click", function () {
            $(".customizer-mix li").removeClass("active");
            $(this).addClass("active");
            var mixLayout = $(this).attr("data-attr");
            $("body").attr("class", mixLayout);
            localStorage.setItem("dark", mixLayout);
        });

        // ── Sidebar icon style ────────────────────────────────────────

        $(".sidebar-setting li").on("click", function () {
            $(".sidebar-setting li").removeClass("active");
            $(this).addClass("active");
            var sidebar = $(this).attr("data-attr");
            $(".sidebar-wrapper").attr("data-sidebar-layout", sidebar);
        });

    });

})(jQuery);

// ── Auto dark / light mode ──────────────────────────────────────────

function applyTheme(theme) {
    if (theme === "light-only") {
        document.body.classList.add(theme, "auto-only");
        document.body.classList.remove("dark-only", "light", "dark-sidebar");
    } else if (theme === "dark-only") {
        document.body.classList.add(theme, "auto-only");
        document.body.classList.remove("light-only", "light", "dark-sidebar");
    }
}

function detectColorScheme() {
    var prefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
    applyTheme(prefersDark ? "dark-only" : "light-only");
}

window.matchMedia("(prefers-color-scheme: dark)").addEventListener("change", function (event) {
    if (document.body.classList.contains("auto-only")) {
        applyTheme(event.matches ? "dark-only" : "light-only");
    }
});
