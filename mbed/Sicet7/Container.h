#pragma once
#include "mbed.h"
#include <string>
#include <Sicet7/Console.h>
#include <Sicet7/NetworkConnection.h>
#include <Sicet7/LightSensor.h>
#include <Sicet7/SoundSensor.h>
#include <Sicet7/TempSensor.h>
#include <Sicet7/DhtThread.h>

namespace Sicet7 {
    class Container {
        private: static Container* instance;
        private: static Mutex* accessLock;
        
        private: Mutex* instanceLock;
        private: Console* consoleInstance;
        private: NetworkConnection* networkConnectionInstance;
        private: DhtThread* dhtInstance;
        private: LightSensor* lightSensorInstance;
        private: SoundSensor* soundSensorInstance;
        private: TempSensor* tempSensorInstance;
        
        /**
         * @return Container*
         */
        public: static Container* get();
        
        /**
         * Constructor
         */
        private: Container();
        
        /**
         * @return std::string
         */
        public: std::string mbedVersion();
        
        /**
         * @return Console*
         */
        public: Console* console();
        
        /**
         * @return NetworkConnection*
         */
        public: NetworkConnection* networkConnection();
        
        /**
         * @return DhtThread*
         */
        public: DhtThread* dht();
        
        /**
         * @return LightSensor*
         */
        public: LightSensor* lightSensor();
        
        /**
         * @return SoundSensor*
         */
        public: SoundSensor* soundSensor();
        
        /**
         * @return TempSensor*
         */
        public: TempSensor* tempSensor();
    };
}