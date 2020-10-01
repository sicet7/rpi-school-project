#pragma once
#include "mbed.h"
#include <string>
namespace Sicet7 {
    class To {
        /**
         * @param const float floatVar
         *
         * @return std::string
         */
        public: static std::string String(const float floatVar);
        
        /**
         * @param const int intVar
         *
         * @return std::string
         */
        public: static std::string String(const int intVar);
        
        /**
         * @param const unsigned int intVar
         *
         * @return std::string
         */
        public: static std::string String(const unsigned int intVar);
    };   
}