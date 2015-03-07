# -*- coding: utf-8 -*-

dir = File.expand_path('../', __FILE__)
bin = File.join(dir, '/vendor/bin')

composer = "#{bin}/composer.phar"
phpunit  = "#{bin}/phpunit"

report = "#{dir}/build/report"
report_coverage = "#{report}/coverage"

log_dir = "#{dir}/build/logs"
log_coverage = "build/logs/clover.xml"

task :default => %(setup)

desc 'Setup application'
task :setup => %w(vendor:setup composer:setup composer:install)

desc 'Run composer'
task :composer => %w(composer:setup composer:update)

namespace :vendor do
  task :setup do
    FileUtils.mkdir_p(bin) unless FileTest.directory?(bin)
  end
end

namespace :composer do
  desc 'Setup composer'
  task :setup do
    unless FileTest.file?(composer)
      sh "curl -sS https://getcomposer.org/installer | " +
         "php -- --install-dir=#{bin}"
    end
  end

  desc 'Run composer install'
  task :install do
    unless FileTest.file?(phpunit)
      sh "#{composer} install"
    end
  end

  desc 'Update composer'
  task :update => %(composer:install)
  task :update do
    sh "#{composer} self-update"
  end
end
