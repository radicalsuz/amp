require 'rubygems'
require 'spec'
require 'pp'

def describe_rake_task(task_name, filename, &block)
  require "rake"

  describe "Rake task #{task_name}" do
    attr_reader :task

    before(:all) do
      @rake = Rake::Application.new
      Rake.application = @rake
      load filename
      @task = Rake::Task[task_name]
    end

    after(:all) do
      Rake.application = nil
    end

    def invoke!
      for action in task.instance_eval { @actions }
        instance_eval(&action)
      end
    end

    instance_eval(&block)
  end
end
