(function () {
    if (typeof self !== 'undefined' && !self.Prism ||
        typeof global !== 'undefined' && !global.Prism) {
        return;
    }

    // `package` is buggy, and creates a conflict with our aim.
    delete Prism.languages.php.package;

    // `keyword` is likely to not be up-to-date, declare ours.
    Prism.languages.php.keyword = /\b(abstract|and|array|as|break|callable|case|catch|class|clone|const|continue|declare|default|die|do|echo|else|elseif|empty|enddeclare|endfor|endforeach|endif|endswitch|endwhile|eval|exit|extends|final|finally|for|foreach|function|global|goto|if|implements|include|include_once|instanceof|insteadof|interface|isset|list|namespace|new|or|print|private|protected|public|require|require_once|return|static|switch|throw|trait|try|unset|use|var|while|xor|yield|yield from)\b/;

    // Introduce `native-type`.
    Prism.languages.insertBefore(
        'php',
        'keyword',
        {
            'native-type': /\b(array|bool|callable|float|int|iterable|string)\b/
        }
    );

    // Introduce `qualified-name`.
    Prism.languages.php['qualified-name'] = /\\?(\w+\\)*\w+/;

    Prism.hooks.add(
        'before-highlight',
        function (env) {
            var noWrapHooks = env.element.classList.contains('no-wrap-hooks');

            if (true === noWrapHooks) {
                Prism.hooks.all.wrap = [];
            } else {
                Prism.hooks.add('wrap', wrapHookShorten);
                Prism.hooks.add('wrap', wrapHookQualifiedName);
            }
        }
    );

    Prism.hooks.add(
        'after-highlight',
        function () {
            Prism.hooks.all.wrap = [];
        }
    );

    function wrapHookShorten(env) {
        if ('php' !== env.language ||
            'keyword' !== env.type) {
            return;
        }

        switch (env.content) {
            case 'public':
            case 'protected':
            case 'private':
                env.content =
                    '<abbr title="' + env.content + '">' +
                    env.content.substring(0, 3) +
                    '</abbr>';

                break;

            case 'function':
                env.content = '<abbr title="' + env.content + '">fn</abbr>';

                break;

        }
    }

    function wrapHookQualifiedName(env) {
        if ('php' !== env.language ||
            'qualified-name' !== env.type) {
            return;
        }

        env.content =
            '<a href="' + qualifiedNameToURL(env.content) + '">' +
            env.content.split('\\').pop() +
            '</a>';
    }

    function qualifiedNameToURL(name) {
        var parts    = name.split('\\');
        var lastPart = parts.pop();

        return parts.reduce(
                function (accumulator, part) {
                    return accumulator + '/' + part.toLowerCase();
                },
                '.'
            ) +
            '/' +
            lastPart + '.html';
    }
})();
