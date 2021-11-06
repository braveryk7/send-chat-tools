function get_files() {
    if git rev-parse --verify HEAD >/dev/null 2>&1; then
        against=HEAD
    else
        against=4b825dc642cb6eb9a060e54bf8d69288fbee4904
    fi

    exec 1>&2

    files=`git diff-index --cached --name-only --diff-filter=AM ${against}`
    is_error=0
    output=""

    php_files=""
    js_files=""
    css_files=""
    for f in ${files}
        do
            extension=${f##*.}
            case ${extension} in
                php)
                    php_files+="${f} ";;
                js | ts | jsx | tsx)
                    js_files+="${f} ";;
                css | scss | sass)
                    css_files+="${f} ";;
            esac
    done
}

function output() {
    if [ ${is_error} -gt 0 ]; then
        is_error=1
        printf "\n  ${ESC}[41m%s${ESC}[m\n" "Commit aborted."
        printf "    ${ESC}[91m%s${ESC}[m\n" "Lint tool found an error."
        echo "${output}\n\n"
    else
        is_error=0
    fi

    exit ${is_error}
}

function set_eslint() {
    if [ -e "${root_path}/node_modules/.bin/eslint" ]; then
        eslint=${root_path}/node_modules/.bin/eslint
        if [ -e "${root_path}/.eslintrc.js" ]; then
            eslint_config=${root_path}/.eslintrc.js
        elif [ -e "${root_path}/.eslintrc.cjs" ]; then
            eslint_config=${root_path}/.eslintrc.cjs
        elif [ -e "${root_path}/.eslintrc.yaml" ]; then
            eslint_config=${root_path}/.eslintrc.yaml
        elif [ -e "${root_path}/.eslintrc.yml" ]; then
            eslint_config=${root_path}/.eslintrc.yml
        elif [ -e "${root_path}/.eslintrc.json" ]; then
            eslint_config=${root_path}/.eslintrc.json
        else
            eslint_config=""
        fi
    else
        eslint="not found"
    fi
}

function set_stylelint() {
    if [ -e "${root_path}/node_modules/.bin/stylelint" ]; then
        stylelint=${root_path}/node_modules/.bin/stylelint
        if [ -e "${root_path}/.stylelintrc" ]; then
            stylelint_config=${root_path}/.stylelintrc
        elif [ -e "${root_path}/.stylelint.config.js" ]; then
            stylelint_config=${root_path}/.stylelint.config.js
        elif [ -e "${root_path}/.stylelint.config.cjs" ]; then
            stylelint_config=${root_path}/.stylelint.config.cjs
        elif [ -e "${root_path}/.stylelintrc.yaml" ]; then
            stylelint_config=${root_path}/.stylelintrc.yaml
        elif [ -e "${root_path}/.stylelintrc.yml" ]; then
            stylelint_config=${root_path}/.stylelintrc.yml
        elif [ -e "${root_path}/.stylelintrc.json" ]; then
            stylelint_config=${root_path}/.stylelintrc.json
        else
            stylelint_config=""
        fi
    else
        stylelint="not found"
    fi
}

function set_phpcs() {
    if [ -e "${root_path}/vendor/bin/phpcs" ]; then
        phpcs=${root_path}/vendor/bin/phpcs
        if [ -e "${root_path}/.phpcs.xml" ]; then
            phpcs_config=${root_path}/.phpcs.xml
        elif [ -e "${root_path}/phpcs.xml" ]; then
            phpcs_config=${root_path}/phpcs.xml
        elif [ -e "${root_path}/.phpcs.xml.dist" ]; then
            phpcs_config=${root_path}/.phpcs.xml.dist
        elif [ -e "${root_path}/phpcs.xml.dist" ]; then
            phpcs_config=${root_path}/phpcs.xml.dist
        else
            phpcs_config=""
        fi
    else
        phpcs="not found"
    fi
}

function execute_eslint() {
    ESC=$(printf '\033')
    eslint_max_warnings=0
    if [ -n "${js_files}" ]; then
        # Check installation of eslint.
        if [ "${eslint}" != "not found" ]; then
            # Check existence of cording standard file.
            if [ ! -e "${eslint_config}" ]; then
                output+='\nNOTE: eslint configuration file is not found.\n'
            fi
            output+=`${eslint} -c ${eslint_config} ${js_files} --max-warnings ${eslint_max_warnings} --color`
            is_error+=$?
        else
            output+='\nNOTE: eslint is not installed. js syntax checking is skipped.'
        fi
    fi
}

function execute_stylelint() {
    stylelint_max_warnings=0
    ESC=$(printf '\033')
    if [ -n "${css_files}" ]; then
        if [ "${stylelint}" != "not found" ]; then
            if [ ! -e "${stylelint_config}" ]; then
                output+='\nNOTE: Stylelint configuration file is not found.\n'
            fi
            output+=`${stylelint} ${css_files} --config ${stylelint_config} --mw ${stylelint_max_warnings} --color`
            is_error+=$?
        else
            output+='\nNOTE: Stylelint is not installed. CSS syntax checking is skipped.'
        fi
    fi
}

function execute_phpcs() {
    ESC=$(printf '\033')
    if [ -n "${php_files}" ]; then
        if [ "${phpcs}" != "not found" ]; then
            if [ ! -e "${phpcs_config}" ]; then
                output+="\n"
                output+='\nNOTE: PHP_CodeSniffer configuration file is not found.\n'
            fi
            output+="\n\n"
            output+=`${phpcs} ${php_files} --colors`
            is_error+=$?
        else
            output+='\nNOTE: PHP_CodeSniffer is not installed. CSS syntax checking is skipped.'
        fi
    fi
}
