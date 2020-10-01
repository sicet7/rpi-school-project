#pragma once
#include "mbed.h"
#include "NetworkInterface.h"

namespace Sicet7 {
    class NetworkConnection {
        
        private: bool connected;
        private: NetworkInterface* interface;
        
        /**
         * Constructor
         */
        public: NetworkConnection();
        
        /**
         * @return NetworkInterface*
         */
        public: NetworkInterface* getInterface();
        
        /**
         * @return bool
         */
        public: bool isConnected();
        
        /**
         * @return const char*
         */
        public: const char* getIpAddress();
        
        /**
         * @return void
         */
        public: void connect();
    };
}