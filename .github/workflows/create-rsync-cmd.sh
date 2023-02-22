#!/bin/sh

# SVN NAME
SVN_NAME="send-chat-tools"

# Output path
output_path="${SVN_NAME}/trunk/"

# Exclude files
# format -> "file or directory name"
excludes=(
	${SVN_NAME}
	".git"
	".github"
	".gitignore"
	".vscode"
	".cache"
	".DS_Store"
	"Thumbs.db"
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
	"phpstan.neon"
	"phpunit.xml"
	"tests"
	# npm
	".npm"
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
	# WordPress
	.wp-env.json
	.wp-env.override.json
)

# Delete trunk directory.
rm ${output_path}

# Create trunk directory.
mkdir ${output_path}

# Get current file, directory.
for file in .[^\.]*;
	do
		current_file_list+=(${file#./*})
	done

# Create exclude list.
cmd_exclude=""
for exclude in "${excludes[@]}";
	do
		cmd_exclude="${cmd_exclude} --exclude '${exclude}'"
	done

cmd="rsync -a ./ ${output_path} ${cmd_exclude}"
eval ${cmd}
