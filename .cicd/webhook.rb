require 'sinatra'
require 'json'

# Spécifier le port
set :port, 8089

ENVIR = ENV["ENVIRONMENT"]

# Définition d'une route
post '/' do
  @payload = JSON.parse(params[:payload])
  if @payload["ref"] == "refs/heads/#{ENVIR}"
    # pull code : 
    f = IO.popen("/tmp/.cicd/pull.sh")
    pull_output = f.readlines
    f.close

    if pull_output.any? { |line| line.include?("Updating") }
      f = IO.popen("/tmp/.cicd/build.sh")
      p f.readlines
      f.close
    else
      puts "deja a jour, skiping."
    end
  end
  status 200
  body "Webhook traité avec succès sur, #{ENVIR}"
end