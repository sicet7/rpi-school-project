#pragma once
#include "mbed.h"
#include <string>

namespace Sicet7 {
    class Console {
        private: Mutex* writeMutex;
        private: Serial* instance;
        
        /**
         * @param const char* output
         *
         * @return void
         */
        private: void writeWithLock(const char* output);
        
        /**
         * Constructor
         */
        public: Console();
        
        /**
         * @param const char* output
         *
         * @return void
         */
        public: void write(const char* output);
        
        /**
         * @param const char* output
         *
         * @return void
         */
        public: void writeLine(const char* output);
        
        /**
         * @param const std::string output
         *
         * @return void
         */
        public: void write(const std::string output);
        
        /**
         * @param const std::string output
         *
         * @return void
         */
        public: void writeLine(const std::string output);
        
        /**
         * @param const std::string* output
         *
         * @return void
         */
        public: void write(const std::string* output);
        
        /**
         * @param const std::string* output
         *
         * @return void
         */
        public: void writeLine(const std::string* output);
    };    
}