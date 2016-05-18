cmake_minimum_required(VERSION 2.8)

function (deps_path library type)
    execute_process (COMMAND "php" "${Deps_DIR}/deps.php" "${type}" "${library}"
        OUTPUT_VARIABLE RESULT)
    string(REPLACE ":" ";" RESULTLIST ${RESULT})
    set (PATHES)
    foreach (RESULTPATH ${RESULTLIST})
        string(STRIP ${RESULTPATH} RESULTPATH)
        if (NOT "${RESULTPATH}" STREQUAL "")
            set (PATHES ${PATHES} ${RESULTPATH})
        endif ()
    endforeach ()
    set (PATHES ${PATHES} PARENT_SCOPE)
endfunction ()

function (deps_add_library library)
    # Check that the library was not already imported
    if (NOT ";${DEPS_IMPORTED};" MATCHES ${library})
        list(APPEND DEPS_IMPORTED ${library})
        set (DEPS_IMPORTED ${DEPS_IMPORTED} PARENT_SCOPE)

        # Adding includes
        deps_path(${library} "includes")
        include_directories (${PATHES})

        # Adding links
        deps_path(${library} "links")

        # Library w/ location
        # Replace name, foo/bar will become foo_bar
        string(REPLACE "/" "_" libraryName "${library}")

        set (k 0)
        foreach (libpath ${PATHES})
            math(EXPR k "${k}+1")
            set(libraryNameId "${libraryName}_${k}")
            add_library(${libraryNameId} UNKNOWN IMPORTED)
            set_property(TARGET ${libraryNameId} PROPERTY IMPORTED_LOCATION ${libpath})
            list(APPEND DEPS_LIBRARIES ${libraryNameId})
            set(DEPS_LIBRARIES ${DEPS_LIBRARIES} PARENT_SCOPE)
        endforeach ()
    endif()
endfunction ()
