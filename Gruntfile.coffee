module.exports = (grunt) ->
  sass = require 'node-sass'
  @initConfig
    pkg: @file.readJSON('package.json')
    watch:
      files: [
        'css/src/**/*.scss'
        'src/*.php'
        'templates/*'
        'img/*'
        'js/*'
      ]
      tasks: ['develop', 'copytostaging']
    postcss:
      pkg:
        options:
          processors: [
            require('autoprefixer')()
          ]
          failOnError: true
        files:
          'css/admin.css': 'css/admin.css'
          'css/style.css': 'css/style.css'
          'css/network-sb-live.css': 'css/network-sb-live.css'
          'css/network-sb-sandbox.css': 'css/network-sb-sandbox.css'
      dev:
        options:
          map: true
          processors: [
            require('autoprefixer')()
          ]
          failOnError: true
        files:
          'css/admin.css': 'css/admin.css'
          'css/style.css': 'css/style.css'
          'css/network-sb-live.css': 'css/network-sb-live.css'
          'css/network-sb-sandbox.css': 'css/network-sb-sandbox.css'
    sass:
      pkg:
        options:
          implementation: sass
          noSourceMap: true
          outputStyle: 'compressed'
          precision: 2
          includePaths: ['node_modules/foundation-sites/scss']
        files:
          'css/admin.css': 'css/src/admin.scss'
          'css/style.css': 'css/src/style.scss'
          'css/network-sb-live.css': 'css/src/network-sb-live.scss'
          'css/network-sb-sandbox.css': 'css/src/network-sb-sandbox.scss'
      dev:
        options:
          implementation: sass
          sourceMap: true
          outputStyle: 'nested'
          precision: 2
          includePaths: ['node_modules/foundation-sites/scss']
        files:
          'css/admin.css': 'css/src/admin.scss'
          'css/style.css': 'css/src/style.scss'
          'css/network-sb-live.css': 'css/src/network-sb-live.scss'
          'css/network-sb-sandbox.css': 'css/src/network-sb-sandbox.scss'
    sasslint:
      options:
        configFile: '.sass-lint.yml'
      target: ['css/**/*.s+(a|c)ss']
    compress:
      main:
        options:
          archive: '<%= pkg.name %>.zip'
        files: [
          {src: ['css/*.css']},
          {src: ['fields/*.php']},
          {src: ['fields/*.json']},
          {src: ['src/*.php']},
          {src: ['*.php']},
          {src: ['readme.md']},
        ]

  @loadNpmTasks 'grunt-contrib-compress'
  @loadNpmTasks 'grunt-contrib-watch'
  @loadNpmTasks 'grunt-sass-lint'
  @loadNpmTasks 'grunt-sass'
  @loadNpmTasks 'grunt-postcss'

  @registerTask 'default', ['sass:pkg', 'postcss:pkg']
  @registerTask 'develop', ['sasslint', 'sass:dev', 'postcss:dev']
  @registerTask 'release', ['compress', 'makerelease']
  @registerTask 'copytostaging', 'Copy all relevant files to this plugin in the libartstest.local server', ->
    done = @async()
    css_files = grunt.file
      .expand {filter: 'isFile' }, ['css/*','src/*','templates/*','img/*','js/*']
      .forEach (filePath) ->
        grunt.file.copy filePath, '../../../../../../libartstest/app/public/wp-content/plugins/wp-user-governance/' + filePath
    return
  @registerTask 'makerelease', 'Set release branch for use in the release task', ->
    done = @async()

    # Define simple properties for release object
    grunt.config 'release.key', process.env.RELEASE_KEY
    grunt.config 'release.file', grunt.template.process '<%= pkg.name %>.zip'

    grunt.util.spawn {
      cmd: 'git'
      args: [ 'rev-parse', '--abbrev-ref', 'HEAD' ]
    }, (err, result, code) ->
      if result.stdout isnt ''
        matches = result.stdout.match /([^\n]+)$/
        grunt.config 'release.branch', matches[1]
        grunt.task.run 'setrepofullname'

      done(err)
      return
    return
  @registerTask 'setrepofullname', 'Set repo full name for use in the release task', ->
    done = @async()

    # Get repository owner and name for use in Github REST requests
    grunt.util.spawn {
      cmd: 'git'
      args: [ 'config', '--get', 'remote.origin.url' ]
    }, (err, result, code) ->
      if result.stdout isnt ''
        grunt.log.writeln 'Remote origin url: ' + result
        matches = result.stdout.match /([^\/:]+)\/([^\/.]+)(\.git)?$/
        grunt.config 'release.repofullname', matches[1] + '/' + matches[2]
        grunt.task.run 'setlasttag'

      done(err)
      return
    return
  @registerTask 'setlasttag', 'Set release message as range of commits', ->
    done = @async()

    grunt.util.spawn {
      cmd: 'git'
      args: [ 'describe', '--tags' ]
    }, (err, result, code) ->
      if result.stdout isnt ''
        matches = result.stdout.match /([^\n]+)/
        grunt.config 'release.lasttag', matches[1] + '..'

      grunt.task.run 'setmsg'

      done(err)
      return
    return
  @registerTask 'setmsg', 'Set gh_release body with commit messages since last release', ->
    done = @async()

    releaserange = grunt.template.process '<%= release.lasttag %>HEAD'

    grunt.util.spawn {
      cmd: 'git'
      args: ['shortlog', releaserange, '--no-merges']
    }, (err, result, code) ->
      msg = grunt.template.process 'Upload <%= release.file %> to your dashboard.'

      if result.stdout isnt ''
        message = result.stdout.replace /(\n)\s\s+/g, '$1- '
        message = message.replace /\s*\[skip ci\]/g, ''
        msg += '\n\n# Changes\n' + message

      grunt.config 'release.msg', msg
      grunt.task.run 'setpostdata'
      done(err)
      return
    return
  @registerTask 'setpostdata', 'Set post object for use in the release task', ->
    val =
      tag_name: 'v' + grunt.config.get 'pkg.version'
      name: grunt.template.process '<%= pkg.name %> (v<%= pkg.version %>)'
      body: grunt.config.get 'release.msg'
      draft: false
      prerelease: false
      # target_commitish: grunt.config.get 'release.branch'
    grunt.config 'release.post', JSON.stringify val
    grunt.log.write JSON.stringify val

    grunt.task.run 'createrelease'
    return
  @registerTask 'createrelease', 'Create a Github release', ->
    done = @async()

    # Create curl arguments for Github REST API request
    args = ['-X', 'POST', '--url']
    args.push grunt.template.process 'https://api.github.com/repos/<%= release.repofullname %>/releases?access_token=<%= release.key %>'
    args.push '--data'
    args.push grunt.config.get 'release.post'
    grunt.log.write 'curl args: ' + args

    # Create Github release using REST API
    grunt.util.spawn {
      cmd: 'curl'
      args: args
    }, (err, result, code) ->
      grunt.log.write '\nResult: ' + result + '\n'
      grunt.log.write 'Error: ' + err + '\n'
      grunt.log.write 'Code: ' + code + '\n'

      if result.stdout isnt ''
        obj = JSON.parse result.stdout
        # Check for error from Github
        if 'errors' of obj and obj['errors'].length > 0
          grunt.fail.fatal 'Github Error'
        else
          # We need the resulting "release" ID value before we can upload the .zip file to it.
          grunt.config 'release.id', obj.id
          grunt.task.run 'uploadreleasefile'

      done(err)
      return
    return
  @registerTask 'uploadreleasefile', 'Upload a zip file to the Github release', ->
    done = @async()

    # Create curl arguments for Github REST API request
    args = ['-X', 'POST', '--header', 'Content-Type: application/zip', '--upload-file']
    args.push grunt.config.get 'release.file'
    args.push '--url'
    args.push grunt.template.process 'https://uploads.github.com/repos/<%= release.repofullname %>/releases/<%= release.id %>/assets?access_token=<%= release.key %>&name=<%= release.file %>'
    grunt.log.write 'curl args: ' + args

    # Upload Github release asset using REST API
    grunt.util.spawn {
      cmd: 'curl'
      args: args
    }, (err, result, code) ->
      grunt.log.write '\nResult: ' + result + '\n'
      grunt.log.write 'Error: ' + err + '\n'
      grunt.log.write 'Code: ' + code + '\n'

      if result.stdout isnt ''
        obj = JSON.parse result.stdout
        # Check for error from Github
        if 'errors' of obj and obj['errors'].length > 0
          grunt.fail.fatal 'Github Error'

      done(err)
      return
    return

  @event.on 'watch', (action, filepath) =>
    @log.writeln('#{filepath} has #{action}')
