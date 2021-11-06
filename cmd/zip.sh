#!/bin/sh

########################################################
###                  Settings start.                 ###
########################################################

# ZIP name
zip_file_name="admin-bar-tools"

# Output path
output_path="./"

# Whether to delete the zip file if it already exists
# Yes = true
# No  = false
delete_flag=true

# Whether to display commands on the console
# Yes = true
# No  = false
show_command_flag=false

# ZIP command quiet
# Yes = true
# No  = false
quiet_flag=true

# Exclude files
# format -> "file or directory name"
excludes=(
	".git"
	".gitignore"
	".vscode"
	".DS_Store"
	"src"
	"cmd"
	"conf"
	# composer
	"vendor"
	"composer.json"
	"composer.lock"
	".phpcs.xml"
	"phpcs.xml"
	".phpcs.xml.dist"
	"phpcs.xml.dist"
	"ruleset.xml"
	# npm
	"node_modules"
	"package.json"
	"package-lock.json"
	".babelrc"
	".babelrc.json"
	".babelrc.js"
	".babelrc.cjs"
	".babelrc.mjs"
	".babel.config.json"
	".babel.config.js"
	".babel.config.cjs"
	".babel.config.mjs"
	".eslintrc.js"
	".eslintrc.cjs"
	".eslintrc.json"
	".eslintrc.yaml"
	".eslintrc.yml"
	".stylelintrc"
	".stylelintrc.js"
	".stylelintrc.cjs"
	".stylelintrc.json"
	".stylelintrc.yaml"
	".stylelintrc.yml"
	"jest.config.js"
	"jest.config.cjs"
	"jest.config.mjs"
	"jest.config.ts"
	"jest.config.json"
	"tsconfig.json"
	"webpack.config.js"
)

########################################################
###                  Settings end.                   ###
########################################################

ESC=$(printf '\033')

zip_file_name+=".zip"

function showhelp() {
	cat <<EOM
  Usage: sh zip.sh

  Use npm script: npm run zip -- <option>

  Options.

  -h	Show help.
  -p	Output path-> ./ | Desktop | Document
  -q	Quiet zip command message
  -s	Show use command

EOM
	exit 0
}

function set_path() {
	if [ $1 = "./" ]; then
		zip_file_name="./${zip_file_name}"
	elif [ $1 = "Desktop" ]; then
		zip_file_name="$HOME/Desktop/${zip_file_name}"
	elif [ $1 = "Documents" ]; then
		zip_file_name="$HOME/Documents/${zip_file_name}"
	fi
}

while getopts "p:qsh" optionKey
	do
		case $optionKey in
			'p')
				set_path ${OPTARG}
				;;
			'q')
				quiet_flag=true
				;;
			's')
				show_command_flag=true
				;;
			'h'|* )
				showhelp
				;;
		esac
	done

if "${delete_flag}"; then
	if [ -e "${zip_file_name}" ]; then
		rm ${zip_file_name}
		printf "${ESC}[91m%s${ESC}[m\n" "  Delete ${zip_file_name} ${ESC}[92msuccessfully${ESC}[m"
	fi
fi

excludes_list=()

for file in "${excludes[@]}"
	do
		excludes_list+=("'*${file}*'")
	done

cmd="zip -r ${zip_file_name} ${output_path} -x ${excludes_list[@]}"

if "${quiet_flag}"; then
	cmd="${cmd} -q"
fi

if "${show_command_flag}"; then
	echo "  ${cmd}"
fi

eval ${cmd}

if [ $? = 0 ]; then
	printf "\n  Create ${zip_file_name} ${ESC}[92msuccessfully${ESC}[m\n\n"
else
	printf "\n${ESC}[91m  Error!! Unsuccessfully${ESC}[m\n\n"
fi
