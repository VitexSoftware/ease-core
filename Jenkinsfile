#!groovy
pipeline {
    agent none

    options {
        ansiColor('xterm')
    }

    stages {
        stage('debian-stable') {
            agent {
                docker { image 'vitexsoftware/debian:stable' }
            }
            steps {
                dir('build/debian/package') {
                    checkout scm
		    buildPackage()
                }
                stash includes: 'dist/**', name: 'dist-debian'
            }

            post {
                success {
                    phpunit '**/target/php-ease-core/phpunit.xml'
                    archiveArtifacts '../*.deb'
                }
            }

        }

        stage('debian-testing') {
            agent {
                docker { image 'vitexsoftware/debian:testing' }
            }
            steps {
                dir('build/debian/package') {
                    sh 'if [ ! -d source ]; then git clone --depth 1 --single-branch $GIT_URL source ; else cd source; git pull; cd ..; fi;'
                    sh 'cd source ; debuild -i -us -uc -b ; cd ..'
                    sh 'mkdir -p $WORKSPACE/dist/debian/ ; mv *.deb *.changes *.build $WORKSPACE/dist/debian/'
                }
            }
        }
        stage('ubuntu-trusty') {
            agent {
                docker { image 'vitexsoftware/trusty:stable' }
            }
            steps {
                dir('build/debian/package') {
                    sh 'if [ ! -d source ]; then git clone --depth 1 --single-branch $GIT_URL source ; else cd source; git pull; cd ..; fi;'
                    sh 'cd source ; debuild -i -us -uc -b ; cd ..'
                    sh 'mkdir -p $WORKSPACE/dist/debian/ ; mv *.deb *.changes *.build $WORKSPACE/dist/debian/'
                }
            }
        }
        stage('ubuntu-hirsute') {
            agent {
                docker { image 'vitexsoftware/ubuntu:testing' }
            }
            steps {
                dir('build/debian/package') {
                    sh 'if [ ! -d source ]; then git clone --depth 1 --single-branch $GIT_URL source ; else cd source; git pull; cd ..; fi;'
                    sh 'cd source ; debuild -i -us -uc -b ; cd ..'
                    sh 'mkdir -p $WORKSPACE/dist/debian/ ; mv *.deb *.changes *.build $WORKSPACE/dist/debian/'
                }
            }
       }
    }
}

def buildPackage() {

    def DISTRO = sh (
	script: 'lsb_release -sd',
        returnStdout: true
    ).trim()

    def SOURCE = sh (
	script: 'dpkg-parsechangelog --show-field Source',
        returnStdout: true
    ).trim()

    def VERSION = sh (
	script: 'dpkg-parsechangelog --show-field Version',
        returnStdout: true
    ).trim()

    ansiColor('vga') {
      echo '\033[42m\033[97mBuild debian package ' + SOURCE + ' v' + VERSION  + ' for ' + DISTRO  + '\033[0m'
    }


//Buster problem: Can't continue: dpkg-parsechangelog is not new enough(needs to be at least 1.17.0)
//
//    debianPbuilder additionalBuildResults: '', 
//	    components: '', 
//	    distribution: DISTRO, 
//	    keyring: '', 
//	    mirrorSite: 'http://deb.debian.org/debian/', 
//	    pristineTarName: ''

    sh 'debuild-pbuilder  -i -us -uc -b'
    sh 'mkdir -p $WORKSPACE/dist/debian/ ; mv ../' + SOURCE + '*_' + VERSION  + '_*.deb ../' + SOURCE + '*_' + VERSION  + '_*.changes ../' + SOURCE + '*_' + VERSION  + '_*.build $WORKSPACE/dist/debian/'
    def files = readFile "${env.WORKSPACE}/build/debian/package/debian/files"
    def packages = files.readLines().collect { it[0.. it.indexOf(' ')] }
    return packages
}
