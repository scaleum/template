(function ($) {
  $.langLocale = $.getCookie("i18n_locale") || false;
  $.setLocale = function (locale) {
    $.langLocale = locale;
    // console.log("[Locale changed]:", $.langLocale);
  };

  $.resolveLocale = function (locale, allowedLocales) {
    const map = {
      en_US: ["en"],
      uk_UA: ["uk", "ua"],
      ro_MD: ["en"],
    };

    const available = map[locale];
    if (!available) {
      return "en";
    }

    if (allowedLocales && allowedLocales.length) {
      for (let i = 0; i < available.length; i++) {
        if (allowedLocales.includes(available[i])) {
          return available[i];
        }
      }
    }

    return available[0]; // если нет совпадения — вернуть первый из списка
  };

  $.loadScriptsLocalized = function (scriptList, onReady) {
    const locale = $.langLocale;

    if (!locale) {
      return setTimeout(() => $.loadScriptsLocalized(scriptList, onReady), 50);
    }

    const scriptLocale = $.resolveLocale(locale, []);
    // Заменяем маркеры {locale} в путях на актуальный язык
    const resolvedScripts = scriptList.map((path) =>
      path.replace("{locale}", scriptLocale)
    );

    // Загружаем последовательно
    $.loadScripts(resolvedScripts)
      .then(onReady)
      .catch((err) => {
        // console.error("Ошибка при загрузке скриптов:", err);
      });
  };

  $(document)
    .ready(function () {
      $.getJSON("/api/settings/i18n/locale", function (data) {
        let lang = false;
        if (typeof data == "object" && data.hasOwnProperty("result")) {
          lang = data.result;
        }

        if (typeof lang == "object") {
          $.setLocale(lang.iso);
          $("[data-lang-current]").data("lang", lang).trigger("draw");
        }
      });

      $.getJSON("/api/settings/i18n/locales", {}, function (data) {
        if (typeof data == "object" && data.hasOwnProperty("result")) {
          let locales = [];
          if (data.result.hasOwnProperty("locales")) {
            locales = data.result.locales;

            $("[data-lang-items]").each(function (i, el) {
              let self = $(this);
              self.empty();
              for (let iso in locales) {
                const lang = locales[iso];

                const $flag = $("<span/>")
                  .addClass("country-flag")
                  .addClass(lang.country)
                  .text(lang.country);

                const $label = $("<span/>").text(lang.language);

                const $link = $("<a/>")
                  .append($flag)
                  .append("&nbsp;")
                  .append($label);

                const $item = $("<li/>")
                  .attr("data-lang", iso)
                  .addClass(lang.is_active ? "active" : "")
                  .data("lang", lang)
                  .append($link);

                self.append($item);
              }
            });
          }
        }
      });

    })
    .on("click", "[data-lang]", function () {
      let self = $(this);
      let lang = self.data("lang");
      let switcher = self.closest("[data-lang-switcher]");
      let label = switcher.find("[data-lang-current]");

      if (lang.iso !== label.data("lang").iso) {
        $.post(
          "/api/settings/i18n/locale",
          { iso: lang.iso },
          function () {
            location.reload();
          },
          "json"
        );
      }
    })
    .on("draw", "[data-lang-current]", function () {
      let lang = $(this).data("lang");
      if (typeof lang == "object") {
        const $flag = $("<span/>")
          .addClass("country-flag")
          .addClass(lang.country)
          .text(lang.country);

        $(this).html($flag);
      }
    });
})(jQuery);
