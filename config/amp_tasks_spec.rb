require 'spec_helper'
describe "amp migrations" do
  before do
    #ENV['MYSQL_PASS'] = 'xxx'
  end

  describe_rake_task "amp:migrate:sql", "amp_tasks.rb" do
    describe "updates list" do
      it "finds the latest version" do
        version.should match(/\d\.\d{1,2}\.\d{1,2}/)
      end
      it "accepts the version set from the command line" do
        ENV['VERSION'] = "3.3.4"
        version.should == "3.3.4"
      end
      it "grabs a list of the related sql updates" do
        ENV['VERSION'] = "3.7.5"
        sql_updates.size.should ==  1
      end
    end

    describe "database list" do
      it "returns a list" do
        databases.size.should > 10
      end
      it "responds to a single DB in the environment" do
        ENV['DB'] = 'safeaccessnow'
        databases.size.should == 1
      end
    end

    describe "database migration" do
      before do
        @test_db = AmpDatabase.new('safeaccessnow' )
        @test_db.stub!(:update)
        stub!( :databases ).and_return( 'safeaccessnow' => @test_db )
      end
      it "should send an update to each database" do
        @test_db.should_receive(:update)
        invoke!
      end
    end

  end

  describe_rake_task "amp:migrate:assets", "amp_tasks.rb" do
    before do
      @test_file = File.join( ( SITE_PATTERN % 'safeaccessnow' ), 'img', 'add.gif' )
      %x[ rm #{@test_file} ]
      stub!(:databases).and_return( { 'safeaccessnow' => AmpDatabase.new('safeaccessnow') } )
    end
    it "makes a list of missing files" do
      invoke!
      @missing_images.should_not be_empty
    end
    it "restores missing files" do
      invoke!
      File.exist?(@test_file).should be_true
    end

    describe "for folders" do
      before do
        @test_file = File.join( ( SITE_PATTERN % 'safeaccessnow' ), 'img', 'license' )
        %x[ rm -rf #{@test_file} ]
        stub!(:databases).and_return( { 'safeaccessnow' => AmpDatabase.new('safeaccessnow') } )
      end
      it "makes a list of default folders" do
        invoke!
        @default_image_folders.should include( 'license' )
      end
      it "makes a list of missing folders" do
        invoke!
        @missing_folders.should_not be_empty
      end
      it "restores missing folders" do
        invoke!
        File.exist?(@test_file).should be_true
      end

    end
  end
end
