cmake_minimum_required(VERSION 2.8)

# Get pathes for a given library and type
# This basically call deps [library] [type] and retrieve the result data in
# a CMake list (PATHES) that is set in the parent scope
function (deps_path library type)
    # Running deps
    execute_process (COMMAND "php" "${Deps_DIR}/deps.php" "${type}" "${library}"
        OUTPUT_VARIABLE RESULT)
    # Replacing separator to get a CMake list
	if (WIN32)
		set (RESULTLIST ${RESULT})
	else ()
		string (REPLACE ":" ";" RESULTLIST "${RESULT}")
	endif ()
    # Appending results to the list, avoiding empty strings
    set (PATHES)
    foreach (RESULTPATH ${RESULTLIST})
        string (STRIP ${RESULTPATH} RESULTPATH)
        if (NOT "${RESULTPATH}" STREQUAL "")
            set (PATHES ${PATHES} ${RESULTPATH})
        endif ()
    endforeach ()
    # Setting it in the parent scope
    set (PATHES ${PATHES} PARENT_SCOPE)
endfunction ()

# This can be used to add a specific library to the inclusion and the
# variable name path
function (deps_add_library_custom library variable)
    # Check that the library was not already imported
    if (NOT ";${DEPS_IMPORTED};" MATCHES ";${library};")
        message("-- deps: adding library ${library}")

        # Marking it as already imported
        list(APPEND DEPS_IMPORTED ${library})
        set (DEPS_IMPORTED ${DEPS_IMPORTED} PARENT_SCOPE)

        # Adding includes
        deps_path(${library} "includes:recursive")
        include_directories (${PATHES})

        # Adding links
        deps_path(${library} "links")

        # Library w/ location
        # Replace name, foo/bar will become foo_bar
        string(REPLACE "/" "_" libraryName "${library}")

        # Each library specified in the deps links section is imported using
        # CMake set_property(), if foo/bar have a links of foo.so and bar.so,
        # is will result in foo_bar_1 (foo.so) and foo_bar_2 (bar.so)
        set (k 0)
        foreach (libpath ${PATHES})
            math(EXPR k "${k}+1")
            set(libraryNameId "${libraryName}_${k}")
            add_library(${libraryNameId} SHARED IMPORTED)
            set_property(TARGET ${libraryNameId} PROPERTY IMPORTED_LOCATION ${libpath})
            list(APPEND ${variable} ${libraryNameId})
            set(${variable} ${${variable}} PARENT_SCOPE)
        endforeach ()
    endif()
endfunction ()
# This can be used to add a specific library to the inclusion and the
# DEPS_LIBRARIES path
function (deps_add_library library)
    deps_add_library_custom(${library} "DEPS_LIBRARIES")
    set(DEPS_LIBRARIES ${DEPS_LIBRARIES} PARENT_SCOPE)
    set(DEPS_IMPORTED ${DEPS_IMPORTED} PARENT_SCOPE)
endfunction ()

# This will add all the libraries that the current project depend on
# to the inclusion pathes and to the DEPS_LIBRARIES variable
function (deps_add_libraries)
    deps_path("porcelain" "info")
    foreach (dep ${PATHES})
        deps_add_library(${dep})
        set(DEPS_LIBRARIES ${DEPS_LIBRARIES} PARENT_SCOPE)
        set(DEPS_IMPORTED ${DEPS_IMPORTED} PARENT_SCOPE)
    endforeach ()
endfunction ()
