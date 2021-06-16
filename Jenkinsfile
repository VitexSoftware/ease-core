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
		    installPackage()
                }
                stash includes: 'dist/**', name: 'dist-buster'
            }

            post {
                success {
		    addToRepository()
                    archiveArtifacts 'dist/debian/'
                }
            }

        }

        stage('debian-testing') {
            agent {
                docker { image 'vitexsoftware/debian:testing' }
            }
            steps {
                dir('build/debian/package') {
                    checkout scm
		    buildPackage()
		    installPackage()
                }
                stash includes: 'dist/**', name: 'dist-bullseye'
            }
            post {
                success {
		    addToRepository()
                    archiveArtifacts 'dist/debian/'
                }
            }
        }
        stage('ubuntu-trusty') {
            agent {
                docker { image 'vitexsoftware/ubuntu:stable' }
            }
            steps {
                dir('build/debian/package') {
                    checkout scm
		    buildPackage()
		    installPackage()
                }
                stash includes: 'dist/**', name: 'dist-trusty'
            }
            post {
                success {
		    addToRepository()
                    archiveArtifacts 'dist/debian/'
                }
            }
        }
        stage('ubuntu-hirsute') {
            agent {
                docker { image 'vitexsoftware/ubuntu:testing' }
            }
            steps {
                dir('build/debian/package') {
                    checkout scm
		    buildPackage()
		    installPackage()
                }
                stash includes: 'dist/**', name: 'dist-hirsute'
            }
            post {
                success {
		    addToRepository()
                    archiveArtifacts 'dist/debian/'
                }
            }
       }
    }
}

def buildPackage() {

    def DIST = sh (
	script: 'lsb_release -sc',
        returnStdout: true
    ).trim()

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
      echo '\033[42m\033[90mBuild debian package ' + SOURCE + ' v' + VERSION  + ' for ' + DISTRO  + '\033[0m'
    }

    def VER = VERSION + '~' + DIST

//Buster problem: Can't continue: dpkg-parsechangelog is not new enough(needs to be at least 1.17.0)
//
//    debianPbuilder additionalBuildResults: '', 
//	    components: '', 
//	    distribution: DIST, 
//	    keyring: '', 
//	    mirrorSite: 'http://deb.debian.org/debian/', 
//	    pristineTarName: ''
    sh 'dch -b -v ' + VER  + ' "' + env.BUILD_TAG  + '"'
    sh 'debuild-pbuilder  -i -us -uc -b'
    sh 'mkdir -p $WORKSPACE/dist/debian/ ; rm -rf $WORKSPACE/dist/debian/* ; mv ../' + SOURCE + '*_' + VER + '_*.deb ../' + SOURCE + '*_' + VER + '_*.changes ../' + SOURCE + '*_' + VER + '_*.build $WORKSPACE/dist/debian/'
}

def addToRepository() {
    def files = readFile "${env.WORKSPACE}/build/debian/package/debian/files"
    def packages = files.readLines().collect { it[0.. it.indexOf(' ')] }
    ansiColor('vga') {
      echo '\033[42m\033[31mBuilded packages ' + packages.join(", ")  + '\033[0m'
    }
}

def installPackage() {
    sh 'find $WORKSPACE/dist/debian/ -iname "*.deb" -exec sudo gdebi --n'
}
