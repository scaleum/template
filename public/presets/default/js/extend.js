(function ($) {
  $.loadScript = function (url, options = {}) {
    return new Promise((resolve, reject) => {
      const script = document.createElement("script");
      script.src = url;
      script.type = "text/javascript";
      script.async = options.async ?? false;
      script.defer = options.defer ?? false;

      script.onload = resolve;
      script.onerror = () =>
        reject(new Error(`Не удалось загрузить скрипт: ${url}`));

      document.head.appendChild(script);
    });
  };

  $.loadStyle = function (url) {
    return new Promise((resolve, reject) => {
      const link = document.createElement("link");
      link.rel = "stylesheet";
      link.type = "text/css";
      link.href = url;
      link.onload = resolve;
      link.onerror = () => reject(new Error(`Failed to load style: ${url}`));
      document.head.appendChild(link);
    });
  };

  $.loadScripts = function (scripts) {
    return scripts.reduce(
      (chain, url) => chain.then(() => $.loadScript(url)),
      Promise.resolve()
    );
  };

  $.loadStyles = function (styles) {
    return styles.reduce(
      (chain, url) => chain.then(() => $.loadStyle(url)),
      Promise.resolve()
    );
  };

  $.getCookie = function (name) {
    const cookies = document.cookie.split(";").map((c) => c.trim());
    for (const cookie of cookies) {
      if (cookie.startsWith(name + "=")) {
        return cookie.substring(name.length + 1);
      }
    }
    return null;
  };
})(jQuery);
